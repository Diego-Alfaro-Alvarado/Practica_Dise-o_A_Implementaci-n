<?php

namespace Tests\Unit;

use App\Models\Ingrediente;
use App\Models\Receta;
use App\Models\RecetaIngrediente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecetaTest extends TestCase
{
    use RefreshDatabase;

    public function test_instanciar_receta_con_ingredientes(): void
    {
        $receta = Receta::create([
            'titulo' => 'Gallo pinto',
            'porciones' => 4,
            'instrucciones' => 'Sofreír la cebolla, agregar los frijoles y luego el arroz.',
        ]);

        $arroz = Ingrediente::create(['nombre' => 'Arroz']);
        $frijoles = Ingrediente::create(['nombre' => 'Frijoles negros']);
        $cebolla = Ingrediente::create(['nombre' => 'Cebolla']);

        $receta->ingredientes()->attach($arroz->id, ['cantidad' => 2, 'unidad_medida' => 'tazas']);
        $receta->ingredientes()->attach($frijoles->id, ['cantidad' => 1.5, 'unidad_medida' => 'tazas']);
        $receta->ingredientes()->attach($cebolla->id, ['cantidad' => 1, 'unidad_medida' => 'unidad']);

        $receta->load('ingredientes');

        $this->assertSame('Gallo pinto', $receta->titulo);
        $this->assertCount(3, $receta->ingredientes);

        $primero = $receta->ingredientes->firstWhere('nombre', 'Arroz');
        $this->assertInstanceOf(RecetaIngrediente::class, $primero->pivot);
        $this->assertEquals(2, $primero->pivot->cantidad);
        $this->assertSame('tazas', $primero->pivot->unidad_medida);

        $this->assertDatabaseHas('receta_ingrediente', [
            'receta_id' => $receta->id,
            'ingrediente_id' => $frijoles->id,
            'unidad_medida' => 'tazas',
        ]);
    }

    public function test_un_ingrediente_puede_estar_en_varias_recetas(): void
    {
        $huevo = Ingrediente::create(['nombre' => 'Huevo']);

        $tortilla = Receta::create(['titulo' => 'Tortilla', 'porciones' => 2, 'instrucciones' => 'Batir y freír.']);
        $queque = Receta::create(['titulo' => 'Queque', 'porciones' => 8, 'instrucciones' => 'Mezclar y hornear.']);

        $tortilla->ingredientes()->attach($huevo->id, ['cantidad' => 3, 'unidad_medida' => 'unidades']);
        $queque->ingredientes()->attach($huevo->id, ['cantidad' => 4, 'unidad_medida' => 'unidades']);

        $this->assertCount(2, $huevo->recetas);
    }
}