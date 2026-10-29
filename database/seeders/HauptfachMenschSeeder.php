<?php

namespace Database\Seeders;

use App\Models\ContentBlock;
use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * Legt die CMS-Seiten zur Aktion "Hauptfach: Mensch" der Schulstiftung der
 * Ev.-Luth. Landeskirche Sachsens an. Idempotent: bestehende Seiten werden
 * aktualisiert, die Blöcke neu aufgebaut.
 *
 * Aufruf: php artisan db:seed --class=HauptfachMenschSeeder
 */
class HauptfachMenschSeeder extends Seeder
{
    private const PHONE = '0351-47933060';

    private const SOURCE = 'https://www.ev-schulen-sachsen.de/';

    public function run(): void
    {
        foreach ($this->pages() as $i => $def) {
            $page = Page::withTrashed()->updateOrCreate(
                ['slug' => $def['slug']],
                [
                    'title' => $def['title'],
                    'content' => null,
                    'meta_title' => $def['meta_title'],
                    'meta_description' => $def['meta_description'],
                    'settings' => ['layout' => 'landing', 'max_width' => 'full'],
                    'is_published' => true,
                    'published_at' => now(),
                ]
            );
            if ($page->trashed()) {
                $page->restore();
            }

            $page->contentBlocks()->delete();
            foreach ($def['blocks'] as $order => $html) {
                ContentBlock::create([
                    'page_id' => $page->id,
                    'type' => 'html',
                    'content' => $html,
                    'settings' => ['block_margin' => 'none'],
                    'order' => $order,
                    'is_visible' => true,
                ]);
            }
        }
    }

    private function subnav(): string
    {
        return '<nav class="hm-subnav" aria-label="Hauptfach: Mensch">'
            .'<a href="/hauptfach-mensch">Überblick</a>'
            .'<a href="/hauptfach-mensch-lehrer">Lehrkräfte</a>'
            .'<a href="/hauptfach-mensch-vorbereitungsdienst">Vorbereitungsdienst</a>'
            .'<a href="/hauptfach-mensch-studierende">Studierende</a>'
            .'<a href="/hauptfach-mensch-schueler">Schüler &amp; FSJ</a>'
            .'<a href="/hauptfach-mensch-faq">Fragen &amp; Antworten</a>'
            .'</nav>';
    }

    private function hero(string $eyebrow, string $small, string $big, string $text, bool $sub = true): string
    {
        return '<header class="hm hm-hero'.($sub ? ' hm-hero--sub' : '').'"><div class="hm-wrap">'
            .'<span class="hm-eyebrow">'.$eyebrow.'</span>'
            .'<h1><span>'.$small.'</span>'.$big.'</h1><p>'.$text.'</p>'
            .'<div class="hm-actions"><a class="hm-btn hm-btn--light" href="/">Aktuelle Stellenangebote</a>'
            .'<a class="hm-btn hm-btn--ghost" href="/hauptfach-mensch-faq">Fragen &amp; Antworten</a></div>'
            .'</div></header>';
    }

    private function cta(): string
    {
        return '<section class="hm hm-section"><div class="hm-wrap"><div class="hm-cta">'
            .'<h2>Neugierig geworden?</h2>'
            .'<p>Bewirb dich direkt bei deiner Wunschschule – auch Initiativbewerbungen sind jederzeit willkommen. '
            .'Fragen beantwortet die Schulstiftung unter '.self::PHONE.'.</p>'
            .'<div class="hm-actions" style="justify-content:center"><a class="hm-btn hm-btn--light" href="/">Stellen ansehen</a>'
            .'<a class="hm-btn hm-btn--ghost" href="'.self::SOURCE.'">Evangelische Schulen in Sachsen</a></div>'
            .'</div></div></section>';
    }

    /** @return array<int, array<string, mixed>> */
    private function pages(): array
    {
        $sub = $this->subnav();

        $overview = [
            $this->hero('Aktion der Evangelischen Schulen in Sachsen', 'Hallo, Schulgestalter!', 'Hauptfach: Mensch',
                'Schule mitgestalten, in offener Atmosphäre unterrichten und eigene Ideen lebendig werden lassen – an den evangelischen Schulen in Sachsen.', false),
            $sub,
            '<section class="hm hm-section"><div class="hm-narrow hm-center">'
                .'<span class="hm-eyebrow">Unser Leitmotiv</span><h2>Jeder Mensch ist Ebenbild Gottes</h2>'
                .'<p class="hm-lead">„Hauptfach: Mensch“ ist der Maßstab, an dem sich die evangelischen Schulen in Sachsen ausrichten: '
                .'Alle werden mit ihren Stärken und Schwächen angenommen und sind eingeladen, sich in die Gemeinschaft einzubringen.</p></div></section>',
            '<section class="hm hm-section hm-section--sand"><div class="hm-wrap">'
                .'<div class="hm-center" style="margin-bottom:2rem"><span class="hm-eyebrow">Dein Weg zu uns</span><h2>Wo möchtest du einsteigen?</h2></div>'
                .'<div class="hm-grid hm-grid--4">'
                .'<a class="hm-card" href="/hauptfach-mensch-lehrer"><div class="hm-badge">1</div><h3>Lehrkräfte</h3><p>Stellenangebote an Grund-, Ober- und Förderschulen sowie Gymnasien – auch für Seiteneinsteiger.</p><span class="hm-more">Mehr erfahren →</span></a>'
                .'<a class="hm-card" href="/hauptfach-mensch-vorbereitungsdienst"><div class="hm-badge">2</div><h3>Vorbereitungsdienst</h3><p>Deine pädagogisch-praktische Ausbildung bis zur Zweiten Staatsprüfung – mit Mentoren an deiner Seite.</p><span class="hm-more">Mehr erfahren →</span></a>'
                .'<a class="hm-card" href="/hauptfach-mensch-studierende"><div class="hm-badge">3</div><h3>Studierende</h3><p>Schnupperwochen, Praktika und Hospitationen – finde heraus, ob Lehrer dein Weg ist.</p><span class="hm-more">Mehr erfahren →</span></a>'
                .'<a class="hm-card" href="/hauptfach-mensch-schueler"><div class="hm-badge">4</div><h3>Schüler &amp; FSJ</h3><p>Schülerpraktikum oder Freiwilliges Soziales Jahr – Perspektivwechsel Schule.</p><span class="hm-more">Mehr erfahren →</span></a>'
                .'</div></div></section>',
            '<section class="hm hm-section"><div class="hm-wrap"><div class="hm-center" style="margin-bottom:2rem"><span class="hm-eyebrow">Was uns ausmacht</span><h2>Sieben Leitgedanken</h2></div>'
                .'<div class="hm-grid hm-grid--3">'
                .'<div class="hm-card"><h3>Vielfalt wertschätzen</h3><p>Die Unterschiedlichkeit von Schülern und Mitarbeitenden gilt als Chance für neue Lösungen.</p></div>'
                .'<div class="hm-card"><h3>Gemeinschaft &amp; Verantwortung</h3><p>Junge Menschen bringen sich ein und übernehmen Verantwortung für Mitmenschen und Umwelt.</p></div>'
                .'<div class="hm-card"><h3>Kultur des Vertrauens</h3><p>Raum zur freien Entfaltung und zu eigenverantwortlichem Handeln.</p></div>'
                .'<div class="hm-card"><h3>Lernende Organisation</h3><p>Evangelische Schulen entwickeln sich kontinuierlich weiter.</p></div>'
                .'<div class="hm-card"><h3>Fehler als Lernchance</h3><p>Kritische Selbstreflexion und Neuanfänge gehören zum Lernen dazu.</p></div>'
                .'<div class="hm-card"><h3>Leistung ganzheitlich</h3><p>Noten allein bestimmen nicht den Wert eines Menschen; soziales Engagement zählt gleichberechtigt.</p></div>'
                .'</div>'
                .'<p class="hm-center" style="margin-top:1.5rem;color:#5b6472">Dazu kommt der Glaube am Schulort: Morgenkreis, Andachten und Schulgottesdienste – offen und ohne Widerspruch zu wissenschaftlichen Erkenntnissen.</p>'
                .'</div></section>',
            '<section class="hm hm-section hm-section--green"><div class="hm-wrap"><div class="hm-grid hm-grid--2" style="align-items:center">'
                .'<div><span class="hm-eyebrow">Das bieten wir</span><h2>Arbeiten, wo Menschen im Mittelpunkt stehen</h2>'
                .'<ul class="hm-list"><li>Respektvolle Begleitung von Schülern</li><li>Familiäre Arbeitsatmosphäre in der Schulgemeinschaft</li>'
                .'<li>Raum zum Ausprobieren und Mitgestalten</li><li>Flexible Vertrags- und Arbeitszeitgestaltung mit sicherer Perspektive</li>'
                .'<li>Einführungskurse, Fachtage und Regionaltreffen für neue Mitarbeitende</li></ul></div>'
                .'<blockquote class="hm-quote">„Fehler sind wertvoll für den Neuanfang.“</blockquote></div></div></section>',
            '<section class="hm hm-section"><div class="hm-narrow hm-center"><h2>Du glaubst, das ist nichts für dich?</h2>'
                .'<p class="hm-lead">Du musst nicht getauft sein. Wichtig ist, dass du dem evangelischen Profil aufgeschlossen begegnest und das Schulleben mitgestalten möchtest.</p>'
                .'<a class="hm-btn hm-btn--green" href="/hauptfach-mensch-faq">Antworten auf häufige Fragen</a></div></section>',
            $this->cta(),
        ];

        $lehrer = [
            $this->hero('Für Lehrkräfte', 'Lehrer werden –', 'mitgestalten!',
                'Evangelische Schulen sind staatlich genehmigte Ersatzschulen in freier Trägerschaft – vielfältig in Größe, Gestaltung und reformpädagogischen Ansätzen.'),
            $sub,
            '<section class="hm hm-section"><div class="hm-wrap"><div class="hm-grid hm-grid--2">'
                .'<div><h2>Freiräume für gute Schule</h2><p>Im Mittelpunkt steht individuelleres Lernen, das unterschiedliche Lerntempi berücksichtigt. Lehrkräfte erhalten mehr Freiheiten, Schule und Unterricht selbst zu gestalten.</p>'
                .'<p>Bei uns kannst du an Grundschulen, Oberschulen, Gymnasien und Förderschulen arbeiten. Den Arbeitsort wählst du selbst und bewirbst dich direkt bei der Schule.</p></div>'
                .'<div class="hm-card"><h3>Wir bieten</h3><ul class="hm-list"><li>Familiäre Arbeitsatmosphäre</li><li>Raum zum Ausprobieren und Mitgestalten</li><li>Flexible Verträge und Arbeitszeiten</li><li>Sichere Perspektive</li></ul></div>'
                .'</div></div></section>',
            '<section class="hm hm-section hm-section--sand"><div class="hm-narrow hm-center"><h2>Quereinstieg möglich</h2>'
                .'<p class="hm-lead">Auch als Seiteneinsteiger bist du willkommen. Die Schule informiert dich über fachliche Voraussetzungen und Wege zur weiteren Qualifikation.</p>'
                .'<div class="hm-actions" style="justify-content:center"><a class="hm-btn hm-btn--green" href="/">Zu den Stellenangeboten</a></div></div></section>',
            $this->cta(),
        ];

        $vorbereitung = [
            $this->hero('Für Lehramtsabsolventen', 'Lehrer werden –', 'mit Hauptfach: Mensch',
                'Absolviere deinen Vorbereitungsdienst für die Zweite Staatsprüfung an einer evangelischen Schule – mit freundlichen Mentoren in familiärer Atmosphäre.'),
            $sub,
            '<section class="hm hm-section"><div class="hm-narrow"><h2>So läuft es ab</h2><div class="hm-steps">'
                .'<div><h3>Bewerbung &amp; Gespräch</h3>Nach der Ersten Staatsprüfung bewirbst du dich bei deiner Wunschschule und lernst sie im Vorstellungsgespräch kennen.</div>'
                .'<div><h3>Wunscheinsatzort angeben</h3>In Absprache mit Schule bzw. Schulträger gibst du die evangelische Schule gegenüber dem Landesamt für Schule und Bildung (LaSuB) als Wunscheinsatzort an.</div>'
                .'<div><h3>Schule meldet die Stelle</h3>Parallel meldet die Schule ihre freie Stelle und dich als Wunschperson dem LaSuB.</div>'
                .'<div><h3>Ausprobieren &amp; wachsen</h3>Du sammelst praktische Erfahrung, wirst begleitet und bereitest dich auf die Zweite Staatsprüfung vor.</div>'
                .'</div></div></section>',
            '<section class="hm hm-section hm-section--sand"><div class="hm-wrap hm-center"><h2>Viele Schulen bilden aus</h2>'
                .'<p class="hm-lead">Ausbildungsplätze gibt es an allen Schularten: Grundschule, Oberschule, Gymnasium und Förderschule. Welche Schulen aktuell Referendare aufnehmen, siehst du in den Stellenangeboten oder im Schulfinder.</p>'
                .'<div class="hm-actions" style="justify-content:center"><a class="hm-btn hm-btn--green" href="/">Stellenangebote</a>'
                .'<a class="hm-btn hm-btn--green" href="https://www.ev-schulen-sachsen.de/">Schulfinder</a></div></div></section>',
            $this->cta(),
        ];

        $studierende = [
            $this->hero('Für Lehramtsstudierende', 'Ausprobieren,', 'ausprobieren lassen.',
                'Wie testest du besser, ob du zum Lehrer geboren bist, als selbst an einer Schule zu arbeiten?'),
            $sub,
            '<section class="hm hm-section"><div class="hm-wrap"><div class="hm-grid hm-grid--2">'
                .'<div class="hm-card"><span class="hm-eyebrow">Zweimal im Jahr</span><h3>Schnupperwochen</h3>'
                .'<p>Die evangelischen Schulen öffnen ihre Türen für Lehramtsstudierende. Fünf Tage lang besuchst du Schulen deiner Wahl, hospitierst im Unterricht und gewinnst einen Eindruck von der Vielfalt unserer Schulen.</p>'
                .'<p>Aktuelle Termine und Anmeldung erfährst du bei der Schulstiftung: '.self::PHONE.'.</p></div>'
                .'<div class="hm-card"><span class="hm-eyebrow">Jederzeit anfragen</span><h3>Praktikum &amp; Hospitation</h3>'
                .'<p>Du bist keinesfalls nur Briefbote und Kaffeekocher: Du begleitest Unterricht, hilfst in Arbeitsgemeinschaften und setzt eigene Projekte um.</p>'
                .'<p>Schulen im ländlichen Raum unterstützen dich bei Logistik und Unterkunftssuche.</p></div>'
                .'</div></div></section>',
            '<section class="hm hm-section hm-section--sand"><div class="hm-narrow"><h2>Anerkennung im Studium</h2>'
                .'<p>Du kannst das Praktikum als „Schulpraktische Studien“ anrechnen lassen: Nimm Kontakt mit deiner Wunschschule auf und bewirb dich. In Absprache mit der Schule erfolgt die Anmeldung über das Praktikumsportal des Freistaates Sachsen – so ist die Anerkennung als Pflichtpraktikum gewährleistet.</p></div></section>',
            $this->cta(),
        ];

        $schueler = [
            $this->hero('Für Schüler und Schulabgänger', 'Perspektivwechsel', 'Schule von der anderen Seite',
                'Die Schule ist vorbei – wie geht’s weiter? Lerne den Arbeitsalltag an unseren Schulen kennen und dich selbst besser.'),
            $sub,
            '<section class="hm hm-section"><div class="hm-wrap"><div class="hm-grid hm-grid--2">'
                .'<div class="hm-card"><span class="hm-eyebrow">Nach dem Abschluss</span><h3>Freiwilliges Soziales Jahr (FSJ)</h3>'
                .'<p>Du schaust in den Schulalltag hinein und lernst dich in praktischer Arbeit besser kennen – als FSJ oder FSJ Pädagogik. Ein FSJ ist eine gute Basis für ein Studium und wird von vielen bei der Vergabe von Studienplätzen berücksichtigt.</p></div>'
                .'<div class="hm-card"><span class="hm-eyebrow">Zur Orientierung</span><h3>Schülerpraktikum</h3>'
                .'<p>Du bist unsicher, wohin es beruflich gehen soll? Im Praktikum fühlst du vor, ob pädagogisches Arbeiten zu dir passt, und sammelst Erfahrungen für deine Berufsorientierung.</p></div>'
                .'</div><div class="hm-center" style="margin-top:2rem"><a class="hm-btn hm-btn--green" href="/">Angebote der Schulen ansehen</a></div></div></section>',
            $this->cta(),
        ];

        $faqs = [
            ['Wer sind die evangelischen Schulen in Sachsen?', 'Staatlich genehmigte Ersatzschulen in freier Trägerschaft – Grund-, Oberschulen, Gymnasien, Förder- und berufsbildende Schulen. Sie sind vielfältig in Größe, Gestaltung und (reform-)pädagogischen Ansätzen und eint das evangelische Profil: Ein christliches Menschenbild liegt dem schulischen Handeln zugrunde.'],
            ['Wo finde ich evangelische Schulen?', 'In ganz Sachsen. Übersicht und Kontakte findest du im <a class="hm-link" href="https://www.ev-schulen-sachsen.de/" target="_blank" rel="noopener">Schulfinder der Schulstiftung</a>.'],
            ['Wie bewerbe ich mich an einer evangelischen Schule?', 'Für Stellen als Lehrkraft, Vorbereitungsdienst, Praktikum oder FSJ bewirbst du dich direkt bei deiner Wunschschule. Initiativbewerbungen sind jederzeit möglich; über aktuelle Kapazitäten geben die Schulen gern Auskunft.'],
            ['Muss ich getauft sein?', 'Nein. Willkommen ist jeder, der dem evangelischen Profil aufgeschlossen begegnet und Rituale wie Morgenkreis, Andachten und gelegentliche Festgottesdienste mitgestalten mag. Offene Fragen klärst du im Gespräch mit der Schule.'],
            ['Wird mein Praktikum für das Studium anerkannt?', 'Ja, als „Schulpraktische Studien“. Nach Absprache mit der Schule meldest du dich über das Praktikumsportal des Freistaates Sachsen an – so gilt es als Pflichtpraktikum.'],
            ['Wie funktioniert der Vorbereitungsdienst?', 'Nach der Ersten Staatsprüfung gibst du die evangelische Schule nach Bewerbung und Vorstellungsgespräch als Wunscheinsatzort beim LaSuB an. Die Schule meldet parallel ihre freie Stelle und dich als Wunschperson.'],
            ['Kann ich als Seiteneinsteiger arbeiten?', 'Ja. Erkundige dich bei deiner Wunschschule nach fachlichen Voraussetzungen und Möglichkeiten der weiteren Qualifikation.'],
        ];
        $faqHtml = '';
        foreach ($faqs as $k => [$q, $a]) {
            $faqHtml .= '<details'.($k === 0 ? ' open' : '').'><summary>'.$q.'</summary><div class="hm-answer">'.$a.'</div></details>';
        }
        $faq = [
            $this->hero('Häufige Fragen', '„Wir“', 'schafft alles.', 'Antworten rund um Bewerbung, Profil, Praktikum und Vorbereitungsdienst.'),
            $sub,
            '<section class="hm hm-section"><div class="hm-narrow hm-faq">'.$faqHtml.'</div></section>',
            $this->cta(),
        ];

        return [
            ['slug' => 'hauptfach-mensch', 'title' => 'Hauptfach: Mensch', 'meta_title' => 'Hauptfach: Mensch – Arbeiten an evangelischen Schulen in Sachsen',
                'meta_description' => 'Lehrer werden, Vorbereitungsdienst, Praktikum oder FSJ an evangelischen Schulen in Sachsen. Alle Infos zur Aktion Hauptfach: Mensch.', 'blocks' => $overview],
            ['slug' => 'hauptfach-mensch-lehrer', 'title' => 'Lehrer werden – mitgestalten!', 'meta_title' => 'Lehrkraft an evangelischen Schulen Sachsens werden',
                'meta_description' => 'Stellen für Lehrkräfte und Seiteneinsteiger an evangelischen Schulen in Sachsen – mit Freiraum zum Mitgestalten.', 'blocks' => $lehrer],
            ['slug' => 'hauptfach-mensch-vorbereitungsdienst', 'title' => 'Vorbereitungsdienst', 'meta_title' => 'Vorbereitungsdienst an evangelischen Schulen in Sachsen',
                'meta_description' => 'Referendariat an einer evangelischen Schule in Sachsen: Ablauf, Bewerbung und Wunscheinsatzort beim LaSuB.', 'blocks' => $vorbereitung],
            ['slug' => 'hauptfach-mensch-studierende', 'title' => 'Studierende', 'meta_title' => 'Schnupperwochen & Praktikum für Lehramtsstudierende',
                'meta_description' => 'Schnupperwochen, Praktika und Hospitationen an evangelischen Schulen in Sachsen – anerkannt als Schulpraktische Studien.', 'blocks' => $studierende],
            ['slug' => 'hauptfach-mensch-schueler', 'title' => 'Schüler & FSJ', 'meta_title' => 'FSJ und Schülerpraktikum an evangelischen Schulen Sachsens',
                'meta_description' => 'Freiwilliges Soziales Jahr und Schülerpraktikum an evangelischen Schulen in Sachsen.', 'blocks' => $schueler],
            ['slug' => 'hauptfach-mensch-faq', 'title' => 'Fragen & Antworten', 'meta_title' => 'FAQ – Arbeiten an evangelischen Schulen in Sachsen',
                'meta_description' => 'Häufige Fragen zu Bewerbung, Taufe, Praktikum, Vorbereitungsdienst und Quereinstieg an evangelischen Schulen.', 'blocks' => $faq],
        ];
    }
}
