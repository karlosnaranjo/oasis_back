<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


trait IndexTrait
{
    public function indexGrid(Request $request, Builder $query)
    {

        // Aplicar filtros si se proporcionan
        $this->applyFilters($request, $query);

        // Aplicar ordenamiento si se proporciona
        $this->applySorts($request, $query);

        // Ejecutar la consulta paginada
        $result = $query->paginate($request->get('perPage'), '*', 'page', $request->get('page'));

        // enviar una respuesta exitosa
        return response(["response" => $result], Response::HTTP_OK);
    }

    protected function applyFilters(Request $request, Builder $query)
    {
        $filtersJson = $request->get('filters');
        $filters = json_decode($filtersJson, true);

        if (is_array($filters) && !empty($filters)) {
            foreach ($filters as $field => $value) {
                $query->where($field, $value);
            }
        }
    }

    protected function applySorts(Request $request, Builder $query)
    {
        $sortsJson = $request->get('sorts');
        $sorts = json_decode($sortsJson, true);

        if (is_array($sorts) && !empty($sorts)) {
            foreach ($sorts as $field => $order) {
                $query->orderBy($field, $order);
            }
        } else {
            //$query->orderBy('created_at', 'desc');
        }
    }
}
