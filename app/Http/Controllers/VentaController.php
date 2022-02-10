<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Venta;
use App\DetalleVenta;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        
        if ($buscar==''){
            $ventas = Venta::join('persona as per','venta.idcliente','=','per.id')
            ->join('users','venta.idusuario','=','users.id')
            ->select('venta.id','venta.tipo_comprobante','venta.serie_comprobante',
            'venta.num_comprobante','venta.fecha_hora','venta.impuesto','venta.total',
            'venta.estado','per.nombre','users.usuario')
            ->orderBy('venta.id', 'desc')->paginate(3);
        }
        else{
            $ventas = Venta::join('persona as per','venta.idcliente','=','persona.id')
            ->join('users','venta.idusuario','=','users.id')
            ->select('venta.id','venta.tipo_comprobante','venta.serie_comprobante',
            'venta.num_comprobante','venta.fecha_hora','venta.impuesto','venta.total',
            'venta.estado','persona.nombre','users.usuario')
            ->where('venta.'.$criterio, 'like', '%'. $buscar . '%')
            ->orderBy('venta.id', 'desc')->paginate(3);
        }
        
        return [
            'pagination' => [
                'total'        => $ventas->total(),
                'current_page' => $ventas->currentPage(),
                'per_page'     => $ventas->perPage(),
                'last_page'    => $ventas->lastPage(),
                'from'         => $ventas->firstItem(),
                'to'           => $ventas->lastItem(),
            ],
            'ventas' => $ventas
        ];
    }

    public function obtenerCabecera(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $venta = Venta::join('persona as per','venta.idcliente','=','per.id')
        ->join('users','venta.idusuario','=','users.id')
        ->select('venta.id','venta.tipo_comprobante','venta.serie_comprobante',
        'venta.num_comprobante','venta.fecha_hora','venta.impuesto','venta.total',
        'venta.estado','per.nombre','users.usuario')
        ->where('venta.id','=',$id)
        ->orderBy('venta.id', 'desc')->take(1)->get();
        
        return ['venta' => $venta];
    }

    public function obtenerDetalles(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $detalles = DetalleVenta::join('articulo as art','detalle_venta.idarticulo','=','art.id')
        ->select('detalle_venta.cantidad','detalle_venta.precio','detalle_venta.descuento', 'art.nombre as articulo')
        ->where('detalle_venta.idventa','=',$id)
        ->orderBy('detalle_venta.id', 'desc')->get();
        
        return ['detalles' => $detalles];
    }

    public function listarPdf(){
        $ventas = Venta::join('persona','venta.idcliente','=','persona.id')
            ->join('users','venta.idusuario','=','users.id')
            ->select('venta.id','venta.tipo_comprobante','venta.serie_comprobante',
            'venta.num_comprobante','venta.fecha_hora','venta.impuesto','venta.total',
            'venta.estado','persona.nombre','users.usuario')
            ->orderBy('venta.id', 'desc')->get();
        $cont=Venta::count();

        $pdf = \PDF::loadView('pdf.ventaspdf',['ventas'=>$ventas,'cont'=>$cont])->setPaper('a4', 'landscape');
        return $pdf->download('ventas.pdf');
    }

    public function pdf(Request $request,$id){
        $venta = Venta::join('persona','venta.idcliente','=','persona.id')
        ->join('users','venta.idusuario','=','users.id')
        ->select('venta.id','venta.tipo_comprobante','venta.serie_comprobante',
        'venta.num_comprobante','venta.created_at','venta.impuesto','venta.total',
        'venta.estado','persona.nombre','persona.tipo_documento','persona.num_documento',
        'persona.direccion','persona.email',
        'persona.telefono','users.usuario')
        ->where('venta.id','=',$id)
        ->orderBy('venta.id', 'desc')->take(1)->get();

        $detalles = DetalleVenta::join('articulo','detalle_venta.idarticulo','=','articulo.id')
        ->select('detalle_venta.cantidad','detalle_venta.precio','detalle_venta.descuento',
        'articulo.nombre as articulo')
        ->where('detalle_venta.idventa','=',$id)
        ->orderBy('detalle_venta.id', 'desc')->get();

        $numventa=Venta::select('num_comprobante')->where('id',$id)->get();

        $pdf = \PDF::loadView('pdf.venta',['venta'=>$venta,'detalles'=>$detalles]);
        return $pdf->download('venta-'.$numventa[0]->num_comprobante.'.pdf');
    }

    public function store(Request $request){
        if (!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $mytime= Carbon::now('America/Mexico_City');

            $venta = new Venta();
            $venta->idcliente = $request->idcliente;
            $venta->idusuario = \Auth::user()->id;
            $venta->tipo_comprobante = $request->tipo_comprobante;
            $venta->serie_comprobante = $request->serie_comprobante;
            $venta->num_comprobante = $request->num_comprobante;
            $venta->fecha_hora = $mytime->toDateString();
            $venta->impuesto = $request->impuesto;
            $venta->total = $request->total;
            $venta->estado = 'Registrado';
            $venta->save();

            $detalles = $request->data;

            foreach($detalles as $ep=>$det)
            {
                $detalle = new DetalleVenta();
                $detalle->idventa = $venta->id;
                $detalle->idarticulo = $det['idarticulo'];
                $detalle->cantidad = $det['cantidad'];
                $detalle->precio = $det['precio'];
                $detalle->descuento = $det['descuento'];         
                $detalle->save();
            }          

            DB::commit();
            return [
                'id' => $venta->id
            ];
        } catch (Exception $e){
            DB::rollBack();
        }
    }

    public function desactivar(Request $request){
        if (!$request->ajax()) return redirect('/');
        $venta = Venta::findOrFail($request->id);
        $venta->estado = 'Anulado';
        $venta->save();
    }
}
