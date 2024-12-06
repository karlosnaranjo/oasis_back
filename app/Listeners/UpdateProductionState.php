<?php

namespace App\Listeners;

use App\Events\ProductionQuantitySaved;
use App\Models\Product;
use App\Models\ProductionPlanning;
use App\Models\ProductionPlanningDetail;
use App\Models\ProductionPlanningQuantity;
use App\Models\ProductionState;
use App\Models\WorkOrder;
use App\Models\WorkOrderDetail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateProductionState
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(ProductionQuantitySaved $event)
    {
        $production_planning_id = $event->production_planning_id;
        // si la Orden ya tiene un estado manual, no lo podemos cambiar
        $pp = ProductionPlanning::join('production_states', 'production_states.id', 'production_planning.production_state_id')
            ->where('production_planning.id', $production_planning_id)
            ->where('production_states.manual_state', 1)
            ->first();
        // si encuentra un registro, retornamos
        if ($pp) {
            return;
        }

        // Obtener la suma de quantities de la tarjeta (ordenada y producida)
        $totalQuantityOrd = ProductionPlanningDetail::where('production_planning_id', $production_planning_id)->sum('quantity');
        $totalQuantityProd = ProductionPlanningQuantity::where('production_planning_id', $production_planning_id)->sum('production_quantity');

        // Calcular el porcentaje de producción
        if ($totalQuantityProd > 0) {
            $percentage = ($totalQuantityProd / $totalQuantityOrd) * 100;
        } else {
            $percentage = 0;
        }

        // Encontrar el estado de producción correspondiente
        $productionState = ProductionState::where('from_percentage', '<=', $percentage)
            ->where('to_percentage', '>=', $percentage)
            ->first();

        if ($productionState) {

            // Actualizar el estado de producción en la tabla work_orders
            // si el estado de produccion esta configurado para cerrar la orden, tambien le va a cambiar el estado
            ProductionPlanning::where('id', $production_planning_id)
                ->update([
                    'production_state_id' => $productionState->id,
                    'finished' => $productionState->close_work_order
                ]);
        }
    }
}
