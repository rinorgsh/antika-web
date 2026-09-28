<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/*
 * Traductions NL / FR / EN du catalogue du simulateur.
 *
 * L'import de l'ancien simulateur a rangé tous les textes dans la case « fr »,
 * alors que les plats, catégories de menu et boissons y étaient rédigés en
 * néerlandais. Résultat : un visiteur FR voyait « Huisgemaakte soep » et un
 * visiteur NL voyait « Mariage ».
 *
 * On ne touche qu'aux textes encore dans leur état importé (seule la case
 * « fr » remplie, avec exactement le texte d'origine) : tout ce qui a déjà été
 * retouché dans l'admin est laissé tel quel. Relançable sans effet.
 */
return new class extends Migration
{
    public function up(): void
    {
        $fields = [
            'event_types' => ['name', 'description'],
            'venues' => ['name', 'short_description', 'description'],
            'event_menu_formulas' => ['name', 'short_description', 'description'],
            'event_menu_categories' => ['name'],
            'event_menu_items' => ['name', 'description'],
            'drink_categories' => ['name'],
            'drink_options' => ['name', 'description'],
            'extra_categories' => ['name'],
            'extra_items' => ['name', 'description'],
        ];

        $map = $this->translations();

        foreach ($fields as $table => $columns) {
            foreach (DB::table($table)->get() as $row) {
                $changes = [];
                foreach ($columns as $column) {
                    $value = json_decode($row->{$column} ?? 'null', true);
                    if (! is_array($value) || array_keys(array_filter($value)) !== ['fr']) {
                        continue;
                    }
                    [$nl, $fr, $en] = $map[trim($value['fr'])] ?? [null, null, null];
                    if ($nl !== null) {
                        $changes[$column] = json_encode(['nl' => $nl, 'fr' => $fr, 'en' => $en], JSON_UNESCAPED_UNICODE);
                    }
                }
                if ($changes) {
                    DB::table($table)->where('id', $row->id)->update($changes);
                }
            }
        }
    }

    public function down(): void
    {
        // Données : rien à annuler.
    }

    /** Texte importé => [nl, fr, en]. */
    private function translations(): array
    {
        $gratin = ['Met gratin dauphinois & warme seizoensgroenten', 'Avec gratin dauphinois & légumes de saison', 'With gratin dauphinois & warm seasonal vegetables'];

        return [
            /* Types d'événement (importés en français) */
            'Mariage' => ['Huwelijk', 'Mariage', 'Wedding'],
            'Organisation complète de votre mariage de rêve' => ['Uw droomhuwelijk, volledig verzorgd', 'Organisation complète de votre mariage de rêve', 'Your dream wedding, fully taken care of'],
            'Fiançailles' => ['Verloving', 'Fiançailles', 'Engagement'],
            'Célébrez vos fiançailles dans un cadre exceptionnel' => ['Vier uw verloving in een uitzonderlijk kader', 'Célébrez vos fiançailles dans un cadre exceptionnel', 'Celebrate your engagement in an exceptional setting'],
            'Anniversaire' => ['Verjaardag', 'Anniversaire', 'Birthday'],
            'Fêtez votre anniversaire en grand' => ['Vier uw verjaardag in stijl', 'Fêtez votre anniversaire en grand', 'Celebrate your birthday in style'],
            'Communion' => ['Communie', 'Communion', 'Communion'],
            'Un moment de partage pour la communion' => ['Een warm familiefeest voor de communie', 'Un moment de partage pour la communion', 'A warm family celebration for the communion'],
            'Baptême' => ['Doopfeest', 'Baptême', 'Christening'],
            'Célébrez le baptême dans un lieu unique' => ['Vier het doopsel op een unieke locatie', 'Célébrez le baptême dans un lieu unique', 'Celebrate the christening in a unique venue'],
            "Événement d'entreprise" => ['Bedrijfsevent', "Événement d'entreprise", 'Corporate event'],
            'Séminaires, galas et événements corporate' => ['Seminaries, galadiners en personeelsfeesten', 'Séminaires, galas et événements corporate', 'Seminars, galas and company parties'],
            'Fête privée' => ['Privéfeest', 'Fête privée', 'Private party'],
            'Organisez votre fête privée sur mesure' => ['Een privéfeest volledig op maat', 'Organisez votre fête privée sur mesure', 'A private party tailored to you'],
            'Baby shower' => ['Babyshower', 'Baby shower', 'Baby shower'],
            'Accueillez bébé avec une fête inoubliable' => ['Verwelkom de baby met een onvergetelijk feest', 'Accueillez bébé avec une fête inoubliable', 'Welcome the baby with an unforgettable party'],
            'Funérailles' => ['Koffietafel', 'Funérailles', 'Funeral reception'],
            'Un lieu de recueillement pour honorer vos proches' => ['Een serene plek om samen uw dierbare te herdenken', 'Un lieu de recueillement pour honorer vos proches', 'A peaceful place to honour your loved one together'],
            'Diplôme / Remise de prix' => ['Diploma- of prijsuitreiking', 'Diplôme / Remise de prix', 'Graduation / award ceremony'],
            'Célébrez les réussites et accomplissements' => ['Vier successen en prestaties', 'Célébrez les réussites et accomplissements', 'Celebrate successes and achievements'],
            'Autre' => ['Ander feest', 'Autre', 'Other'],
            "Tout autre type d'événement sur mesure" => ['Elk ander evenement, volledig op maat', "Tout autre type d'événement sur mesure", 'Any other event, fully tailored'],

            /* Salles (noms propres identiques ; textes importés en français) */
            'La Villa Feestzaal' => ['La Villa Feestzaal', 'La Villa Feestzaal', 'La Villa Feestzaal'],
            'Antika Restaurant Zaal' => ['Antika Restaurant Zaal', 'Antika Restaurant Zaal', 'Antika Restaurant Zaal'],
            'Privat Salon' => ['Privat Salon', 'Privat Salon', 'Privat Salon'],
            'Open Air Terras' => ['Open Air Terras', 'Open Air Terras', 'Open Air Terras'],
            'Compleet Domain' => ['Compleet Domein', 'Domaine complet', 'Entire estate'],
            'Salle principale avec vestiaire pour grands événements' => ['Grote feestzaal met vestiaire voor grote feesten', 'Salle principale avec vestiaire pour grands événements', 'Main banquet hall with cloakroom for large events'],
            'Salle restaurant élégante pour événements de taille moyenne' => ['Stijlvolle restaurantzaal voor middelgrote feesten', 'Salle restaurant élégante pour événements de taille moyenne', 'Elegant restaurant room for mid-sized events'],
            'Salon privé intime pour petits événements' => ['Intiem privésalon voor kleine gezelschappen', 'Salon privé intime pour petits événements', 'Intimate private lounge for small gatherings'],
            'Terrasse extérieure pour événements en plein air' => ['Buitenterras voor feesten in openlucht', 'Terrasse extérieure pour événements en plein air', 'Outdoor terrace for open-air events'],
            'Domaine complet privatisé pour grands événements' => ['Het volledige domein, exclusief voor uw grote feest', 'Domaine complet privatisé pour grands événements', 'The entire estate, exclusively yours for a large event'],
            'Notre salle principale, La Villa Feestzaal, offre un cadre majestueux pour vos grands événements. Avec une capacité de 180 places assises, un parking privé et un vestiaire, cette salle est idéale pour les mariages et grandes célébrations.' => [
                'Onze grootste zaal, La Villa Feestzaal, biedt een indrukwekkend kader voor uw grote feesten. Met 180 zitplaatsen, een eigen parking en een vestiaire is ze ideaal voor huwelijken en grote vieringen.',
                'Notre salle principale, La Villa Feestzaal, offre un cadre majestueux pour vos grands événements. Avec une capacité de 180 places assises, un parking privé et un vestiaire, cette salle est idéale pour les mariages et grandes célébrations.',
                'Our main hall, La Villa Feestzaal, offers a majestic setting for large events. With 180 seats, private parking and a cloakroom, it is ideal for weddings and big celebrations.',
            ],
            "L'Antika Restaurant Zaal combine l'élégance d'un restaurant avec le confort d'une salle de réception privée. Capacité de 100 places assises avec parking." => [
                'De Antika Restaurant Zaal combineert de elegantie van een restaurant met het comfort van een privé-feestzaal. 100 zitplaatsen, met parking.',
                "L'Antika Restaurant Zaal combine l'élégance d'un restaurant avec le confort d'une salle de réception privée. Capacité de 100 places assises avec parking.",
                'The Antika Restaurant Zaal combines the elegance of a restaurant with the comfort of a private function room. 100 seats, with parking.',
            ],
            'Le Privat Salon est un espace intime et raffiné, parfait pour les petits événements, réunions ou dîners privés. Capacité de 40 places assises.' => [
                'Het Privat Salon is een intieme, verfijnde ruimte, perfect voor kleine feesten, vergaderingen of privédiners. 40 zitplaatsen.',
                'Le Privat Salon est un espace intime et raffiné, parfait pour les petits événements, réunions ou dîners privés. Capacité de 40 places assises.',
                'The Privat Salon is an intimate, refined space, perfect for small events, meetings or private dinners. 40 seats.',
            ],
            "Notre terrasse en plein air offre un cadre unique pour vos événements estivaux. Profitez d'un espace extérieur aménagé avec une capacité de 40 places assises." => [
                'Ons openluchtterras biedt een uniek kader voor uw zomerse feesten: een ingerichte buitenruimte met 40 zitplaatsen.',
                "Notre terrasse en plein air offre un cadre unique pour vos événements estivaux. Profitez d'un espace extérieur aménagé avec une capacité de 40 places assises.",
                'Our open-air terrace offers a unique setting for summer events: a landscaped outdoor space seating 40.',
            ],
            "Privatisez l'ensemble du domaine Antika Baba pour un événement d'exception. Avec une capacité totale de 350 places assises, le Compleet Domain inclut toutes les salles et espaces du lieu." => [
                'Privatiseer het volledige domein Antika voor een uitzonderlijk evenement. Met in totaal 350 zitplaatsen omvat het alle zalen en ruimtes van de locatie.',
                "Privatisez l'ensemble du domaine Antika pour un événement d'exception. Avec une capacité totale de 350 places assises, il inclut toutes les salles et espaces du lieu.",
                'Book the entire Antika estate for an exceptional event. With 350 seats in total, it includes every room and space on site.',
            ],

            /* Formules (sans prix : le site n'affiche aucun tarif) */
            'Classic Celebration' => ['Classic Celebration', 'Classic Celebration', 'Classic Celebration'],
            'Signature Event' => ['Signature Event', 'Signature Event', 'Signature Event'],
            'Basic Buffet' => ['Basic Buffet', 'Basic Buffet', 'Basic Buffet'],
            'Premium Buffet' => ['Premium Buffet', 'Premium Buffet', 'Premium Buffet'],
            'Deluxe Buffet' => ['Deluxe Buffet', 'Deluxe Buffet', 'Deluxe Buffet'],
            'Formule assise classique à 55€/personne' => ['Klassiek menu aan tafel', 'Formule assise classique', 'Classic seated menu'],
            'Formule assise premium à 65€/personne' => ['Premium menu aan tafel', 'Formule assise premium', 'Premium seated menu'],
            'Formule buffet à 60€/personne' => ['Buffetformule', 'Formule buffet', 'Buffet'],
            'Formule buffet premium à 70€/personne' => ['Premium buffet', 'Formule buffet premium', 'Premium buffet'],
            'Formule buffet deluxe à 80€/personne' => ['Deluxe buffet', 'Formule buffet deluxe', 'Deluxe buffet'],
            'Une formule classique et savoureuse pour célébrer vos événements. Menu assis avec des plats traditionnels revisités.' => [
                'Een klassieke, smaakvolle formule voor uw feest: een menu aan tafel met traditionele gerechten in een nieuw jasje.',
                'Une formule classique et savoureuse pour célébrer vos événements. Menu assis avec des plats traditionnels revisités.',
                'A classic, flavourful formula for your celebration: a seated menu of traditional dishes, reinvented.',
            ],
            'Notre formule signature avec des plats raffinés et des options premium. Idéale pour les événements haut de gamme.' => [
                'Onze signatureformule met verfijnde gerechten en premiumopties. Ideaal voor stijlvolle feesten.',
                'Notre formule signature avec des plats raffinés et des options premium. Idéale pour les événements haut de gamme.',
                'Our signature formula with refined dishes and premium options. Ideal for upscale events.',
            ],
            'Un buffet généreux avec une sélection de plats variés. Parfait pour des événements conviviaux et décontractés.' => [
                'Een royaal buffet met een gevarieerde selectie gerechten. Perfect voor gezellige, ongedwongen feesten.',
                'Un buffet généreux avec une sélection de plats variés. Parfait pour des événements conviviaux et décontractés.',
                'A generous buffet with a varied selection of dishes. Perfect for relaxed, convivial events.',
            ],
            'Un buffet premium avec une large variété de plats et des options supplémentaires pour impressionner vos invités.' => [
                'Een premium buffet met een ruime keuze aan gerechten en extra opties om uw gasten te verrassen.',
                'Un buffet premium avec une large variété de plats et des options supplémentaires pour impressionner vos invités.',
                'A premium buffet with a wide choice of dishes and extra options to impress your guests.',
            ],
            'Notre formule buffet la plus complète avec des mets d\'exception, fruits de mer et une sélection de desserts raffinés.' => [
                'Onze meest uitgebreide buffetformule, met uitzonderlijke gerechten, zeevruchten en verfijnde desserts.',
                'Notre formule buffet la plus complète avec des mets d\'exception, fruits de mer et une sélection de desserts raffinés.',
                'Our most complete buffet, with exceptional dishes, seafood and a selection of refined desserts.',
            ],

            /* Catégories de menu (importées en néerlandais) */
            'Voorgerecht' => ['Voorgerecht', 'Entrée', 'Starter'],
            'Hoofdgerecht' => ['Hoofdgerecht', 'Plat principal', 'Main course'],
            'Dessert' => ['Dessert', 'Dessert', 'Dessert'],
            'Voorgerechtbuffet' => ['Voorgerechtenbuffet', "Buffet d'entrées", 'Starter buffet'],
            'Hoofdgerechtbuffet' => ['Hoofdgerechtenbuffet', 'Buffet de plats', 'Main course buffet'],

            /* Plats (importés en néerlandais) */
            'Huisgemaakte soep' => ['Huisgemaakte soep', 'Soupe maison', 'Homemade soup'],
            'Kaaskroketten of carpaccio' => ['Kaaskroketten of carpaccio', 'Croquettes au fromage ou carpaccio', 'Cheese croquettes or carpaccio'],
            'Mezze-mix (koud & warm)' => ['Mezze-mix (koud & warm)', 'Assortiment de mezzés (froids & chauds)', 'Mezze platter (hot & cold)'],
            'Kip' => ['Kip', 'Poulet', 'Chicken'],
            'Met saus van de chef' => ['Met saus van de chef', 'Sauce du chef', "With the chef's sauce"],
            'Kalkoen' => ['Kalkoen', 'Dinde', 'Turkey'],
            'Huisgemaakte aardappelpuree' => ['Huisgemaakte aardappelpuree', 'Purée maison', 'Homemade mashed potatoes'],
            'Bijgerecht' => ['Bijgerecht', 'Accompagnement', 'Side dish'],
            'Warme seizoensgroenten' => ['Warme seizoensgroenten', 'Légumes de saison', 'Warm seasonal vegetables'],
            'Huisgemaakte gebakjes' => ['Huisgemaakte gebakjes', 'Pâtisseries maison', 'Homemade pastries'],
            'Huisgemaakte cocoacake' => ['Huisgemaakte cacaocake', 'Gâteau au cacao maison', 'Homemade cocoa cake'],
            'Fruit' => ['Fruit', 'Fruits', 'Fruit'],
            'Scampi-garnalen' => ['Scampi', 'Scampis', 'Scampi'],
            'Zalmcarpaccio' => ['Zalmcarpaccio', 'Carpaccio de saumon', 'Salmon carpaccio'],
            'Garnaal- of kaaskroketten' => ['Garnaal- of kaaskroketten', 'Croquettes aux crevettes ou au fromage', 'Shrimp or cheese croquettes'],
            'Steak – filet mignon' => ['Steak – filet mignon', 'Steak – filet mignon', 'Steak – filet mignon'],
            $gratin[0] => $gratin,
            'Ovengebakken rundsteak' => ['Ovengebakken rundsteak', 'Rôti de bœuf au four', 'Oven-roasted beef'],
            'Gefileerde zeebaars' => ['Gefileerde zeebaars', 'Filet de bar', 'Sea bass fillet'],
            'Met gratin dauphinois & warme seizoensgroenten (supplement +€2/pp)' => [
                'Met gratin dauphinois & warme seizoensgroenten (met supplement)',
                'Avec gratin dauphinois & légumes de saison (avec supplément)',
                'With gratin dauphinois & warm seasonal vegetables (with supplement)',
            ],
            'Lamskoteletten' => ['Lamskoteletten', "Côtelettes d'agneau", 'Lamb chops'],
            'Met gratin dauphinois & warme seizoensgroenten (supplement +€8/pp)' => [
                'Met gratin dauphinois & warme seizoensgroenten (met supplement)',
                'Avec gratin dauphinois & légumes de saison (avec supplément)',
                'With gratin dauphinois & warm seasonal vegetables (with supplement)',
            ],
            'Trileçe' => ['Trileçe', 'Trileçe', 'Trileçe'],
            'Tiramisu' => ['Tiramisu', 'Tiramisu', 'Tiramisu'],
            'Chocolademousse' => ['Chocolademousse', 'Mousse au chocolat', 'Chocolate mousse'],
            'Calamares' => ['Calamares', 'Calamars', 'Calamari'],
            'Kaaskroketten' => ['Kaaskroketten', 'Croquettes au fromage', 'Cheese croquettes'],
            'Carpaccio' => ['Carpaccio', 'Carpaccio', 'Carpaccio'],
            'Kipfilet' => ['Kipfilet', 'Filet de poulet', 'Chicken fillet'],
            'Kefta' => ['Kefta', 'Kefta', 'Kefta'],
            'Rundsvlees' => ['Rundsvlees', 'Bœuf', 'Beef'],
            'Groenten' => ['Groenten', 'Légumes', 'Vegetables'],
            'Saladebar' => ['Saladebar', 'Bar à salades', 'Salad bar'],
            'Aardappelen' => ['Aardappelen', 'Pommes de terre', 'Potatoes'],
            'Baklava' => ['Baklava', 'Baklava', 'Baklava'],
            'Cocosgebak' => ['Kokosgebak', 'Gâteau coco', 'Coconut cake'],
            'Fruitsalade' => ['Fruitsalade', 'Salade de fruits', 'Fruit salad'],
            'Scampi' => ['Scampi', 'Scampis', 'Scampi'],
            'Loempia' => ['Loempia', 'Nems', 'Spring rolls'],
            'Garnaalkroketten' => ['Garnaalkroketten', 'Croquettes aux crevettes', 'Shrimp croquettes'],
            'Tzatziki' => ['Tzatziki', 'Tzatziki', 'Tzatziki'],
            'Russische salade' => ['Russische salade', 'Salade russe', 'Russian salad'],
            'Steak' => ['Steak', 'Steak', 'Steak'],
            'Vis' => ['Vis', 'Poisson', 'Fish'],
            'Kroketten' => ['Kroketten', 'Croquettes', 'Croquettes'],
            'Pasta' => ['Pasta', 'Pâtes', 'Pasta'],
            'Profiteroles' => ['Profiteroles', 'Profiteroles', 'Profiteroles'],
            'Chocoladecake' => ['Chocoladecake', 'Gâteau au chocolat', 'Chocolate cake'],
            'Kaas- en garnaalkroketten' => ['Kaas- en garnaalkroketten', 'Croquettes au fromage et aux crevettes', 'Cheese and shrimp croquettes'],
            'Caprese-spiesjes' => ['Caprese-spiesjes', 'Brochettes caprese', 'Caprese skewers'],
            'Bruschetta' => ['Bruschetta', 'Bruschetta', 'Bruschetta'],
            'Tonijn met kappertjes' => ['Tonijn met kappertjes', 'Thon aux câpres', 'Tuna with capers'],
            'Parmaham met meloen' => ['Parmaham met meloen', 'Jambon de Parme et melon', 'Parma ham with melon'],
            'Tomaat met garnalen' => ['Tomaat met garnalen', 'Tomate aux crevettes', 'Tomato with shrimps'],
            'Extra viskeuze – schaaldieren (zeevruchten)' => ['Extra viskeuze – schaaldieren (zeevruchten)', 'Poisson en supplément – crustacés (fruits de mer)', 'Extra fish choice – shellfish (seafood)'],
            'Chocoladebrownie' => ['Chocoladebrownie', 'Brownie au chocolat', 'Chocolate brownie'],
            'Cheesecake' => ['Cheesecake', 'Cheesecake', 'Cheesecake'],
            'Panna cotta' => ['Panna cotta', 'Panna cotta', 'Panna cotta'],

            /* Boissons (importées en néerlandais) */
            'Softdrinks' => ['Frisdranken', 'Softs', 'Soft drinks'],
            'Bier' => ['Bier', 'Bière', 'Beer'],
            'Speciaal Bier' => ['Speciaalbier', 'Bières spéciales', 'Speciality beer'],
            'Wijn' => ['Wijn', 'Vin', 'Wine'],
            'Premium Wijn' => ['Premium wijn', 'Vin premium', 'Premium wine'],
            'Koffie & Thee' => ['Koffie & thee', 'Café & thé', 'Coffee & tea'],
            'Sterke drank' => ['Sterke drank', 'Spiritueux', 'Spirits'],
            'Champagne' => ['Champagne', 'Champagne', 'Champagne'],
            'Aperitief' => ['Aperitief', 'Apéritif', 'Aperitif'],
            'Softdrinks (4 keuzes)' => ['Frisdranken (4 naar keuze)', 'Softs (4 au choix)', 'Soft drinks (choice of 4)'],
            'Bier van het vat' => ['Bier van het vat', 'Bière pression', 'Draught beer'],
            'Bier Leffe/Karmeliet' => ['Leffe / Karmeliet', 'Leffe / Karmeliet', 'Leffe / Karmeliet'],
            'Huiswijn' => ['Huiswijn', 'Vin maison', 'House wine'],
            'Premium wijn' => ['Premium wijn', 'Vin premium', 'Premium wine'],
            'Koffie & thee' => ['Koffie & thee', 'Café & thé', 'Coffee & tea'],
            'Fles cava/prosecco' => ['Fles cava / prosecco', 'Bouteille de cava / prosecco', 'Bottle of cava / prosecco'],
            'Aperitiefreceptie' => ['Aperitiefreceptie', 'Réception apéritive', 'Drinks reception'],
            'Aperitiefreceptie met hapjes' => ['Aperitiefreceptie met hapjes', 'Réception apéritive avec bouchées', 'Drinks reception with bites'],

            /* Extras (importés en français) */
            'Décoration' => ['Decoratie', 'Décoration', 'Decoration'],
            'Animation & Musique' => ['Animatie & muziek', 'Animation & musique', 'Entertainment & music'],
            'Services supplémentaires' => ['Extra diensten', 'Services supplémentaires', 'Additional services'],
            'Pâtisserie' => ['Patisserie', 'Pâtisserie', 'Cakes & sweets'],
            'Pack Basique (inclus dans la salle)' => ['Basispakket (inbegrepen bij de zaal)', 'Pack Basique (inclus dans la salle)', 'Basic pack (included with the venue)'],
            'Décoration de base incluse avec la location de salle' => ['Basisdecoratie inbegrepen bij de zaalhuur', 'Décoration de base incluse avec la location de salle', 'Basic decoration included with the venue hire'],
            'Pack Premium (fleurs, bougies, centres de table)' => ['Premiumpakket (bloemen, kaarsen, tafelstukken)', 'Pack Premium (fleurs, bougies, centres de table)', 'Premium pack (flowers, candles, centrepieces)'],
            'Décoration premium avec arrangements floraux, bougies et centres de table' => ['Premium decoratie met bloemstukken, kaarsen en tafelstukken', 'Décoration premium avec arrangements floraux, bougies et centres de table', 'Premium decoration with floral arrangements, candles and centrepieces'],
            'Pack Luxe (scénographie complète)' => ['Luxepakket (volledige aankleding)', 'Pack Luxe (scénographie complète)', 'Luxury pack (full styling)'],
            "Scénographie complète sur mesure pour un événement d'exception" => ['Volledige aankleding op maat voor een uitzonderlijk feest', "Scénographie complète sur mesure pour un événement d'exception", 'Full bespoke styling for an exceptional event'],
            'Ballons hélium (lot de 50)' => ['Heliumballonnen (set van 50)', 'Ballons hélium (lot de 50)', 'Helium balloons (set of 50)'],
            "Lot de 50 ballons à l'hélium personnalisables" => ['Set van 50 heliumballonnen, te personaliseren', "Lot de 50 ballons à l'hélium personnalisables", 'Set of 50 customisable helium balloons'],
            'Photobooth avec accessoires' => ['Photobooth met accessoires', 'Photobooth avec accessoires', 'Photo booth with props'],
            'Photobooth professionnel avec accessoires et impressions illimitées' => ['Professionele photobooth met accessoires en onbeperkt afdrukken', 'Photobooth professionnel avec accessoires et impressions illimitées', 'Professional photo booth with props and unlimited prints'],
            'DJ professionnel' => ['Professionele DJ', 'DJ professionnel', 'Professional DJ'],
            "DJ professionnel pour toute la durée de l'événement" => ['Professionele DJ voor het hele feest', "DJ professionnel pour toute la durée de l'événement", 'Professional DJ for the whole event'],
            'Groupe live / Orchestre' => ['Liveband / orkest', 'Groupe live / Orchestre', 'Live band / orchestra'],
            'Groupe de musique live ou orchestre' => ['Liveband of orkest', 'Groupe de musique live ou orchestre', 'Live band or orchestra'],
            'Sonorisation premium' => ['Premium geluidsinstallatie', 'Sonorisation premium', 'Premium sound system'],
            'Système de sonorisation haut de gamme' => ['Hoogwaardige geluidsinstallatie', 'Système de sonorisation haut de gamme', 'High-end sound system'],
            "Éclairage d'ambiance LED" => ['Led-sfeerverlichting', "Éclairage d'ambiance LED", 'LED mood lighting'],
            "Éclairage LED d'ambiance personnalisable" => ['Aanpasbare led-sfeerverlichting', "Éclairage LED d'ambiance personnalisable", 'Customisable LED mood lighting'],
            'Écran & projecteur' => ['Scherm & beamer', 'Écran & projecteur', 'Screen & projector'],
            'Écran et vidéoprojecteur pour présentations ou diaporamas' => ['Scherm en beamer voor presentaties of diavoorstellingen', 'Écran et vidéoprojecteur pour présentations ou diaporamas', 'Screen and projector for presentations or slideshows'],
            'Wedding planner / Coordinateur' => ['Weddingplanner / coördinator', 'Wedding planner / Coordinateur', 'Wedding planner / coordinator'],
            'Coordinateur dédié pour la planification et le jour J' => ['Vaste coördinator voor de voorbereiding en de grote dag', 'Coordinateur dédié pour la planification et le jour J', 'Dedicated coordinator for the planning and the big day'],
            'Photographe professionnel' => ['Professionele fotograaf', 'Photographe professionnel', 'Professional photographer'],
            'Photographe professionnel pour capturer vos moments' => ['Professionele fotograaf die uw mooiste momenten vastlegt', 'Photographe professionnel pour capturer vos moments', 'Professional photographer to capture your moments'],
            'Vidéaste' => ['Videograaf', 'Vidéaste', 'Videographer'],
            'Vidéaste professionnel pour un film souvenir' => ['Professionele videograaf voor een blijvende herinnering', 'Vidéaste professionnel pour un film souvenir', 'Professional videographer for a keepsake film'],
            'Voiturier' => ['Valet parking', 'Voiturier', 'Valet parking'],
            'Service de voiturier pour vos invités' => ['Valet parking voor uw gasten', 'Service de voiturier pour vos invités', 'Valet parking for your guests'],
            'Service de sécurité' => ['Bewaking', 'Service de sécurité', 'Security'],
            "Agent de sécurité pour la durée de l'événement" => ['Bewakingsagent tijdens het hele feest', "Agent de sécurité pour la durée de l'événement", 'Security guard for the duration of the event'],
            'Candy bar' => ['Candybar', 'Candy bar', 'Candy bar'],
            'Bar à bonbons et confiseries pour vos invités' => ['Snoepbar met lekkernijen voor uw gasten', 'Bar à bonbons et confiseries pour vos invités', 'Sweets and candy bar for your guests'],
            'Fontaine à chocolat' => ['Chocoladefontein', 'Fontaine à chocolat', 'Chocolate fountain'],
            'Fontaine à chocolat avec fruits et accompagnements' => ['Chocoladefontein met fruit en toppings', 'Fontaine à chocolat avec fruits et accompagnements', 'Chocolate fountain with fruit and dippers'],
            'Pièce montée classique' => ['Klassieke pièce montée', 'Pièce montée classique', 'Classic croquembouche'],
            'Pièce montée traditionnelle' => ['Traditionele pièce montée', 'Pièce montée traditionnelle', 'Traditional croquembouche'],
            'Wedding cake fondant' => ['Bruidstaart in fondant', 'Wedding cake fondant', 'Fondant wedding cake'],
            'Gâteau de mariage en pâte à sucre personnalisé' => ['Gepersonaliseerde bruidstaart in suikerpasta', 'Gâteau de mariage en pâte à sucre personnalisé', 'Personalised sugar-paste wedding cake'],
            'Sweet table complète' => ['Volledige sweet table', 'Sweet table complète', 'Full sweet table'],
            'Table de desserts variés et décorée' => ['Gedecoreerde tafel met gevarieerde desserts', 'Table de desserts variés et décorée', 'Decorated table with a variety of desserts'],
            'Baklava plateau (50 pièces)' => ['Baklavaschotel (50 stuks)', 'Plateau de baklava (50 pièces)', 'Baklava platter (50 pieces)'],
            'Plateau de 50 pièces de baklava maison' => ['Schotel met 50 stuks huisgemaakte baklava', 'Plateau de 50 pièces de baklava maison', 'Platter of 50 pieces of homemade baklava'],
        ];
    }
};
