<?php

namespace App\Services;

use App\Models\MenuItem;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Publie la carte régénérée dans le repo GitHub Pages (menu.antika-resto.ovh)
 * en un seul commit atomique (API Git Data : blobs → tree → commit → ref).
 *
 * Les fichiers texte (food-data.js…) et les éventuelles images (option A)
 * partent dans le même commit. GitHub Pages redéploie ensuite tout seul.
 */
class MenuPublisher
{
    private string $owner;
    private string $repo;
    private string $branch;
    private string $basePath;
    private string $token;

    public function __construct(private MenuBuilder $builder)
    {
        $cfg = config('antika.github');
        $this->owner = $cfg['owner'];
        $this->repo = $cfg['repo'];
        $this->branch = $cfg['branch'];
        $this->basePath = trim($cfg['base_path'], '/');
        $this->token = $cfg['token'];
    }

    public function isConfigured(): bool
    {
        return $this->token !== '';
    }

    /**
     * @param  array<string,string>  $extraFiles  chemin(relatif repo) => contenu binaire (images)
     * @return string  URL du commit créé
     */
    public function publish(string $message, array $extraFiles = []): string
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Jeton GitHub manquant (ANTIKA_GITHUB_TOKEN).');
        }

        // Traite les photos/logos téléversés : bytes à pousser + met à jour les références en base
        $extraFiles = array_merge($this->syncUploads(), $extraFiles);

        // Fichiers de données régénérés depuis la base
        $files = [];
        foreach ($this->builder->all() as $name => $content) {
            $files["{$this->basePath}/{$name}"] = ['content' => $content, 'binary' => false];
        }
        foreach ($extraFiles as $path => $content) {
            $files[$path] = ['content' => $content, 'binary' => true];
        }

        // 1. réf de la branche -> commit de base -> tree de base
        $ref = $this->api('GET', "git/ref/heads/{$this->branch}");
        $baseCommitSha = $ref['object']['sha'];
        $baseCommit = $this->api('GET', "git/commits/{$baseCommitSha}");
        $baseTreeSha = $baseCommit['tree']['sha'];

        // 2. un blob par fichier
        $tree = [];
        foreach ($files as $path => $file) {
            $blob = $this->api('POST', 'git/blobs', $file['binary']
                ? ['content' => base64_encode($file['content']), 'encoding' => 'base64']
                : ['content' => $file['content'], 'encoding' => 'utf-8']);
            $tree[] = ['path' => $path, 'mode' => '100644', 'type' => 'blob', 'sha' => $blob['sha']];
        }

        // 3. nouvel arbre + commit + avance la branche
        $newTree = $this->api('POST', 'git/trees', ['base_tree' => $baseTreeSha, 'tree' => $tree]);
        $commit = $this->api('POST', 'git/commits', [
            'message' => $message,
            'tree' => $newTree['sha'],
            'parents' => [$baseCommitSha],
        ]);
        $this->api('PATCH', "git/refs/heads/{$this->branch}", ['sha' => $commit['sha']]);

        return $commit['html_url'] ?? "https://github.com/{$this->owner}/{$this->repo}/commit/{$commit['sha']}";
    }

    /**
     * Déplace les photos/logos téléversés vers le repo et met à jour les
     * références (photo/logo) en base. Renvoie [chemin repo => bytes].
     *
     * @return array<string,string>
     */
    private function syncUploads(): array
    {
        $extra = [];
        $items = MenuItem::with('category')
            ->whereNotNull('photo_upload')->orWhereNotNull('logo_upload')
            ->get();

        foreach ($items as $item) {
            $surface = $item->category->surface;

            if ($item->photo_upload && Storage::disk('public')->exists($item->photo_upload)) {
                $bytes = Storage::disk('public')->get($item->photo_upload);
                $ext = strtolower(pathinfo($item->photo_upload, PATHINFO_EXTENSION)) ?: 'jpg';
                if ($surface === 'food') {
                    // Le rendu ajoute "photos/" + ".jpg" ; la référence = clé sans extension.
                    $extra["photos/{$item->slug}.jpg"] = $bytes;
                    $item->photo = $item->slug;
                } else {
                    $extra["drinks/{$item->slug}.{$ext}"] = $bytes;
                    $item->photo = "{$item->slug}.{$ext}";
                }
                Storage::disk('public')->delete($item->photo_upload);
                $item->photo_upload = null;
            }

            if ($item->logo_upload && Storage::disk('public')->exists($item->logo_upload)) {
                $bytes = Storage::disk('public')->get($item->logo_upload);
                $ext = strtolower(pathinfo($item->logo_upload, PATHINFO_EXTENSION)) ?: 'png';
                $extra["logos/{$item->slug}.{$ext}"] = $bytes;
                $item->logo = "{$item->slug}.{$ext}";
                Storage::disk('public')->delete($item->logo_upload);
                $item->logo_upload = null;
            }

            $item->save();
        }

        return $extra;
    }

    private function api(string $method, string $path, array $body = []): array
    {
        $res = Http::withToken($this->token)
            ->acceptJson()
            ->withHeaders(['X-GitHub-Api-Version' => '2022-11-28', 'User-Agent' => 'antika-admin'])
            ->send($method, "https://api.github.com/repos/{$this->owner}/{$this->repo}/{$path}",
                $body ? ['json' => $body] : []);

        if ($res->failed()) {
            throw new RuntimeException("GitHub {$method} {$path} — ".$res->status().' : '.$res->body());
        }

        return $res->json();
    }
}
