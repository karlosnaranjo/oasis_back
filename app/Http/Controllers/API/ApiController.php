<?php

namespace App\Http\Controllers\API;

use App\Traits\ApiResponserTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Planner X",
 *      description="Production Planner",
 *      x={
 *          "logo": {
 *              "url": "{{url('/icons/planner_color.png')}}",
 *          }
 *      },
 *      @OA\Contact(
 *          email="asierra@avanceintegral.com"
 *      ),
 * )
 */
 /*          @OA\License(
             name="Apache 2.0",
            url="https://www.apache.org/licenses/LICENSE-2.0.html"
         )
*/
class ApiController extends Controller
{
    use ApiResponserTrait;
}
