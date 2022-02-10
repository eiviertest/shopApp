<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Ingreso;
use App\DetalleIngreso;

class IngresoController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!$request->ajax()) return redirect('/');
        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if($buscar == ''){
            $ingresos = Ingreso::join('persona as per', 'ingreso.idproveedor', 'per.id')
            ->join('users as us', 'ingreso.idusuario', 'us.id')
            ->select('ingreso.id', 'ingreso.tipo_comprobante', 'ingreso.serie_comprobante', 
            'ingreso.num_comprobante', 'ingreso.fecha_hora', 'ingreso.impuesto', 
            'ingreso.total', 'ingreso.estado', 'per.nombre', 'us.usuario')
            ->orderBy('ingreso.id', 'desc')
            ->paginate(10);
        }else{
            $ingresos = Ingreso::join('persona as per', 'ingreso.idproveedor', 'per.id')
            ->join('users as us', 'ingreso.idusuario', 'us.id')
            ->select('ingreso.id', 'ingreso.tipo_comprobante', 'ingreso.serie_comprobante', 
            'ingreso.num_comprobante', 'ingreso.fecha_hora', 'ingreso.impuesto', 
            'ingreso.total', 'ingreso.estado', 'per.nombre', 'us.usuario')
            ->where('ingreso'.$criterio, 'like', '%'.$buscar.'%')
            ->orderBy('ingreso.id', 'desc')
            ->paginate(10);
        }
        return [
            'pagination' => [
                'total' => $ingresos->total(),
                'current_page' => $ingresos->currentPage(),
                'per_page' => $ingresos->perPage(),
                'last_page' => $ingresos->lastPage(),
                'from' => $ingresos->firstItem(),
                'to' => $ingresos->lastItem()
            ],
            'ingresos' => $ingresos
        ];
    }

    public function obtenerCabecera(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $ingreso = Ingreso::join('persona as per','ingreso.idproveedor','=','per.id')
        ->join('users','ingreso.idusuario','=','users.id')
        ->select('ingreso.id','ingreso.tipo_comprobante','ingreso.serie_comprobante',
        'ingreso.num_comprobante','ingreso.fecha_hora','ingreso.impuesto','ingreso.total',
        'ingreso.estado','per.nombre','users.usuario')
        ->where('ingreso.id','=',$id)
        ->orderBy('ingreso.id', 'desc')->take(1)->get();
        
        return ['ingreso' => $ingreso];
    }

    public function obtenerDetalles(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $detalles = DetalleIngreso::join('articulo as art','detalle_ingreso.idarticulo','=','art.id')
        ->select('detalle_ingreso.cantidad','detalle_ingreso.precio','art.nombre as articulo')
        ->where('detalle_ingreso.idingreso','=',$id)
        ->orderBy('detalle_ingreso.id', 'desc')->get();
        
        return ['detalles' => $detalles];
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!$request->ajax()) return redirect('/');
        try{
            DB::beginTransaction();
            $mytime= Carbon::now('America/Mexico_City');
            $ingreso = new Ingreso();
            $ingreso->idproveedor = $request->idproveedor;
            $ingreso->idusuario = \Auth::user()->id;
            $ingreso->tipo_comprobante = $request->tipo_comprobante;
            $ingreso->serie_comprobante = $request->serie_comprobante;
            $ingreso->num_comprobante = $request->num_comprobante;
            $ingreso->fecha_hora = $mytime->toDateString();
            $ingreso->impuesto = $request->impuesto;
            $ingreso->total = $request->total;
            $ingreso->estado = 'Registrado';
            $ingreso->save();
            $detalles = $request->data;
            foreach($detalles as $ep=>$det){
                $detalle = new DetalleIngreso();
                $detalle->idingreso = $ingreso->id;
                $detalle->idarticulo = $det['idarticulo'];
                $detalle->cantidad = $det['cantidad'];
                $detalle->precio = $det['precio'];          
                $detalle->save();
            }          
            DB::commit();
        } catch (Exception $e){
            DB::rollBack();
        }
    }

    /**
     * Change condition's value to 0 (disable).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function desactivar(Request $request)
    {
        if(!$request->ajax()) return redirect('/');
        $ingreso = Ingreso::findOrFail($request->id);
        $ingreso->estado = 'Anulado';
        $ingreso->save();
    }
}
