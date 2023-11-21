<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         \App\Models\User::factory(10)->create();

            DB::table('users')->insert(['name' => 'Pau', 'email' => 'pau.tort@uvic.cat', 'password' => bcrypt('123456')]);
            DB::table('seccio')->insert(['nom'=>'Telefonia mòbil','descripcio'=>'telèfons mòbils, smartwatches, auriculars i altres accessoris']);
            DB::table('seccio')->insert(['nom'=>'Ordinadors','descripcio'=>'Ordinadors portàtils i accessoris']);
            DB::table('producte')->insert(['nom'=>'IPhone 15','descripcio'=>'telefon mòbil', 'preu_unitari'=>'1499.99','seccio_id'=>'1']);
            DB::table('producte')->insert(['nom'=>'Google Pixel 8','descripcio'=>'telefon mòbil', 'preu_unitari'=>'899.99','seccio_id'=>'1']);
            DB::table('producte')->insert(['nom'=>'ASUS TUF GAMING','descripcio'=>'ordinador portàtil', 'preu_unitari'=>'1299.99','seccio_id'=>'2']);
            DB::table('producte')->insert(['nom'=>'Chromebook','descripcio'=>'ordinador portàtil', 'preu_unitari'=>'399.99','seccio_id'=>'2']);
            DB::table('botiga')->insert(['poblacio'=>'Roda de Ter','adreca'=>'Carrer Nou, nº 25','telefon'=>'938256145','correu'=>'botigaroda@botiga.cat']);
            DB::table('botiga')->insert(['poblacio'=>'Vic','adreca'=>'Plaça Major, nº 10','telefon'=>'938542662','correu'=>'botigavic@botiga.cat']);
            DB::table('botiga')->insert(['poblacio'=>'Manlleu','adreca'=>'Plaça Fra Bernardí, nº 1','telefon'=>'938234512','correu'=>'botigamanlleu@botiga.cat']);
            DB::table('comanda')->insert(['botiga_id'=>'1','usuari_id'=>'1']);
            DB::table('comanda')->insert(['botiga_id'=>'2','usuari_id'=>'3']);
            DB::table('estoc_botiga')->insert(['botiga_id'=>'1','producte_id'=>'1','quantitat'=>'3']);
            DB::table('estoc_botiga')->insert(['botiga_id'=>'2','producte_id'=>'1','quantitat'=>'8']);
            DB::table('estoc_botiga')->insert(['botiga_id'=>'1','producte_id'=>'3','quantitat'=>'5']);
            DB::table('estoc_botiga')->insert(['botiga_id'=>'3','producte_id'=>'2','quantitat'=>'4']);
            DB::table('estoc_botiga')->insert(['botiga_id'=>'2','producte_id'=>'4','quantitat'=>'6']);
            DB::table('linia_comanda')->insert(['comanda_id'=>'1','producte_id'=>'1','quantitat'=>'4']);
            DB::table('linia_comanda')->insert(['comanda_id'=>'1','producte_id'=>'3','quantitat'=>'6']);
            DB::table('linia_comanda')->insert(['comanda_id'=>'2','producte_id'=>'1','quantitat'=>'2']);
            DB::table('linia_comanda')->insert(['comanda_id'=>'2','producte_id'=>'4','quantitat'=>'8']);
    }
}
