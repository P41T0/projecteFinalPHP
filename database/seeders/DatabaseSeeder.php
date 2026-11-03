<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach (['users', 'seccio', 'producte', 'botiga', 'comanda', 'estoc_botiga', 'linia_comanda'] as $table) {
            if (DB::table($table)->exists()) {
                $this->command?->info("Skipping demo seeding because the {$table} table already contains data.");

                return;
            }
        }

        DB::transaction(fn () => $this->seedData());
    }

    private function seedData(): void
    {
        $users = \App\Models\User::factory(10)->create();
        $phoneSectionId = (string) Str::uuid();
        $computerSectionId = (string) Str::uuid();
        $productIds = [(string) Str::uuid(), (string) Str::uuid(), (string) Str::uuid(), (string) Str::uuid()];
        $storeIds = [(string) Str::uuid(), (string) Str::uuid(), (string) Str::uuid()];
        $orderIds = [(string) Str::uuid(), (string) Str::uuid()];

        DB::table('users')->insert([
            ['id' => (string) Str::uuid(), 'name' => 'Pau', 'email' => 'pau.tort@uvic.cat', 'password' => bcrypt('provapag.123456'), 'admin' => true],
            ['id' => (string) Str::uuid(), 'name' => 'Usuari Admin', 'email' => 'usuariadministrador@p41t.com', 'password' => bcrypt('UsuariAdmin.1234'), 'admin' => true],
        ]);
        DB::table('seccio')->insert([
            ['id' => $phoneSectionId, 'nom' => 'Telefonia mòbil', 'nom_es' => 'Telefonia mòbil', 'nom_en' => 'Telefonia mòbil', 'descripcio' => 'telèfons mòbils, smartwatches, auriculars i altres accessoris', 'descripcio_es' => 'telèfons mòbils, smartwatches, auriculars i altres accessoris', 'descripcio_en' => 'telèfons mòbils, smartwatches, auriculars i altres accessoris', 'mostra_sec' => true],
            ['id' => $computerSectionId, 'nom' => 'Ordinadors', 'nom_es' => 'Ordinadors', 'nom_en' => 'Ordinadors', 'descripcio' => 'Ordinadors portàtils i accessoris', 'descripcio_es' => 'Ordinadors portàtils i accessoris', 'descripcio_en' => 'Ordinadors portàtils i accessoris', 'mostra_sec' => true],
        ]);
        DB::table('producte')->insert([
            ['id' => $productIds[0], 'nom' => 'IPhone 15', 'nom_es' => 'IPhone 15', 'nom_en' => 'IPhone 15', 'descripcio' => 'telefon mòbil', 'descripcio_es' => 'telefon mòbil', 'descripcio_en' => 'telefon mòbil', 'foto' => 'iPhone15_black.webp', 'preu_unitari' => '1499.99', 'mostra_prod' => true, 'seccio_id' => $phoneSectionId],
            ['id' => $productIds[1], 'nom' => 'Google Pixel 8', 'nom_es' => 'Google Pixel 8', 'nom_en' => 'Google Pixel 8', 'descripcio' => 'telefon mòbil', 'descripcio_es' => 'telefon mòbil', 'descripcio_en' => 'telefon mòbil', 'foto' => 'google_pixel_8_5g_rosa_Frontback.webp', 'preu_unitari' => '899.99', 'mostra_prod' => true, 'seccio_id' => $phoneSectionId],
            ['id' => $productIds[2], 'nom' => 'ASUS TUF GAMING', 'nom_es' => 'ASUS TUF GAMING', 'nom_en' => 'ASUS TUF GAMING', 'descripcio' => 'ordinador portàtil', 'descripcio_es' => 'ordinador portàtil', 'descripcio_en' => 'ordinador portàtil', 'foto' => 'audio_pd.png', 'preu_unitari' => '1299.99', 'mostra_prod' => true, 'seccio_id' => $computerSectionId],
            ['id' => $productIds[3], 'nom' => 'Chromebook', 'nom_es' => 'Chromebook', 'nom_en' => 'Chromebook', 'descripcio' => 'ordinador portàtil', 'descripcio_es' => 'ordinador portàtil', 'descripcio_en' => 'ordinador portàtil', 'foto' => 'lenovo-laptop-lenovo-500e-gen3-hero.webp', 'preu_unitari' => '399.99', 'mostra_prod' => true, 'seccio_id' => $computerSectionId],
        ]);
        DB::table('botiga')->insert([
            ['id' => $storeIds[0], 'poblacio' => 'Roda de Ter', 'adreca' => 'Carrer Nou, nº 25', 'telefon' => '938256145', 'correu' => 'botigaroda@botiga.cat'],
            ['id' => $storeIds[1], 'poblacio' => 'Vic', 'adreca' => 'Plaça Major, nº 10', 'telefon' => '938542662', 'correu' => 'botigavic@botiga.cat'],
            ['id' => $storeIds[2], 'poblacio' => 'Manlleu', 'adreca' => 'Plaça Fra Bernardí, nº 1', 'telefon' => '938234512', 'correu' => 'botigamanlleu@botiga.cat'],
        ]);
        DB::table('comanda')->insert([
            ['id' => $orderIds[0], 'botiga_id' => $storeIds[0], 'usuari_id' => $users[0]->id],
            ['id' => $orderIds[1], 'botiga_id' => $storeIds[1], 'usuari_id' => $users[2]->id],
        ]);
        DB::table('estoc_botiga')->insert([
            ['id' => (string) Str::uuid(), 'botiga_id' => $storeIds[0], 'producte_id' => $productIds[0], 'quantitat' => 3],
            ['id' => (string) Str::uuid(), 'botiga_id' => $storeIds[1], 'producte_id' => $productIds[0], 'quantitat' => 8],
            ['id' => (string) Str::uuid(), 'botiga_id' => $storeIds[2], 'producte_id' => $productIds[0], 'quantitat' => 0],
            ['id' => (string) Str::uuid(), 'botiga_id' => $storeIds[0], 'producte_id' => $productIds[2], 'quantitat' => 5],
            ['id' => (string) Str::uuid(), 'botiga_id' => $storeIds[1], 'producte_id' => $productIds[2], 'quantitat' => 0],
            ['id' => (string) Str::uuid(), 'botiga_id' => $storeIds[2], 'producte_id' => $productIds[2], 'quantitat' => 0],
            ['id' => (string) Str::uuid(), 'botiga_id' => $storeIds[0], 'producte_id' => $productIds[1], 'quantitat' => 0],
            ['id' => (string) Str::uuid(), 'botiga_id' => $storeIds[1], 'producte_id' => $productIds[1], 'quantitat' => 0],
            ['id' => (string) Str::uuid(), 'botiga_id' => $storeIds[2], 'producte_id' => $productIds[1], 'quantitat' => 4],
            ['id' => (string) Str::uuid(), 'botiga_id' => $storeIds[0], 'producte_id' => $productIds[3], 'quantitat' => 0],
            ['id' => (string) Str::uuid(), 'botiga_id' => $storeIds[1], 'producte_id' => $productIds[3], 'quantitat' => 6],
            ['id' => (string) Str::uuid(), 'botiga_id' => $storeIds[2], 'producte_id' => $productIds[3], 'quantitat' => 0],
        ]);
        DB::table('linia_comanda')->insert([
            ['id' => (string) Str::uuid(), 'comanda_id' => $orderIds[0], 'producte_id' => $productIds[0], 'quantitat' => 4],
            ['id' => (string) Str::uuid(), 'comanda_id' => $orderIds[0], 'producte_id' => $productIds[2], 'quantitat' => 6],
            ['id' => (string) Str::uuid(), 'comanda_id' => $orderIds[1], 'producte_id' => $productIds[0], 'quantitat' => 2],
            ['id' => (string) Str::uuid(), 'comanda_id' => $orderIds[1], 'producte_id' => $productIds[3], 'quantitat' => 8],
        ]);
    }
}
