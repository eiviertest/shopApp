<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Articulo;
use App\Categoria;

class ArticuloController extends Controller
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
            $articulos = Articulo::join('categoria as cat', 'articulo.idcategoria', '=','cat.id')
            ->select('articulo.id', 'articulo.idcategoria', 'articulo.codigo', 'articulo.nombre', 'cat.nombre as nombre_categoria', 'articulo.precio_venta', 'articulo.stock', 'articulo.descripcion', 'articulo.condicion')
            ->orderBy('articulo.id', 'desc')->paginate(10);
        }else{

            $articulos = Articulo::join('categoria as cat', 'articulo.idcategoria', '=','cat.id')
            ->select('articulo.id', 'articulo.idcategoria', 'articulo.codigo', 'articulo.nombre', 'cat.nombre as nombre_categoria', 'articulo.precio_venta', 'articulo.stock', 'articulo.descripcion', 'articulo.condicion')
            ->where('articulo'.$criterio, 'like', '%'.$buscar.'%')
            ->orderBy('articulo.id', 'desc')->paginate(10);
        }
        return [
            'pagination' => [
                'total' => $articulos->total(),
                'current_page' => $articulos->currentPage(),
                'per_page' => $articulos->perPage(),
                'last_page' => $articulos->lastPage(),
                'from' => $articulos->firstItem(),
                'to' => $articulos->lastItem()
            ],
            'articulos' => $articulos
        ];
    }

    public function listarArticulo(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        
        if ($buscar==''){
            $articulos = Articulo::join('categoria as cat','articulo.idcategoria','=','cat.id')
            ->select('articulo.id','articulo.idcategoria','articulo.codigo','articulo.nombre','cat.nombre as nombre_categoria','articulo.precio_venta','articulo.stock','articulo.descripcion','articulo.condicion')
            ->where('articulo.condicion', '1')
            ->orderBy('articulo.id', 'desc')->paginate(10);
        } else{
            $articulos = Articulo::join('categoria as cat','articulo.idcategoria','=','cat.id')
            ->select('articulo.id','articulo.idcategoria','articulo.codigo','articulo.nombre','cat.nombre as nombre_categoria','articulo.precio_venta','articulo.stock','articulo.descripcion','articulo.condicion')
            ->where('articulo.condicion', '1')
            ->where('articulo.'.$criterio, 'like', '%'. $buscar . '%')
            ->orderBy('articulo.id', 'desc')->paginate(10);
        }
        
        return ['articulos' => $articulos];
    }
 
    public function listarArticuloVenta(Request $request){
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        
        if ($buscar==''){
            $articulos = Articulo::join('categoria as cat','articulo.idcategoria','=','cat.id')
            ->select('articulo.id','articulo.idcategoria','articulo.codigo','articulo.nombre',
            'cat.nombre as nombre_categoria','articulo.precio_venta','articulo.stock',
            'articulo.descripcion','articulo.condicion')
            ->where('articulo.stock','>','0')
            ->orderBy('articulo.id', 'desc')->paginate(10);
        }
        else{
            $articulos = Articulo::join('categoria as cat','articulo.idcategoria','=','cat.id')
            ->select('articulo.id','articulo.idcategoria','articulo.codigo','articulo.nombre',
            'cat.nombre as nombre_categoria','articulo.precio_venta','articulo.stock',
            'articulo.descripcion','articulo.condicion')
            ->where('articulo.'.$criterio, 'like', '%'. $buscar . '%')
            ->where('articulo.stock','>','0')
            ->orderBy('articulo.id', 'desc')->paginate(10);
        }
        

        return ['articulos' => $articulos];
    }

    public function listarPdf(){
        $articulos = Articulo::join('categoria as cat','articulo.idcategoria','=','cat.id')
            ->select('articulo.id','articulo.idcategoria','articulo.codigo','articulo.nombre',
            'cat.nombre as nombre_categoria','articulo.precio_venta','articulo.stock',
            'articulo.descripcion','articulo.condicion')
            ->orderBy('articulo.nombre', 'asc')->get();

        $cont=Articulo::count();

        $pdf = \PDF::loadView('pdf.articulospdf',['articulos'=>$articulos,'cont'=>$cont])->setPaper('a4', 'portrait');
        return $pdf->download('articulos.pdf');
    }

    /**
     * Return row articulo
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function buscarArticulo(Request $request){
        if (!$request->ajax()) return redirect('/');

        $filtro = $request->filtro;
        $articulos = Articulo::where('codigo','=', $filtro)
        ->select('id','nombre')->orderBy('nombre', 'asc')->take(1)->get();

        return ['articulos' => $articulos];
    }

    public function buscarArticuloVenta(Request $request){
        if (!$request->ajax()) return redirect('/');

        $filtro = $request->filtro;
        $articulos = Articulo::where('codigo','=', $filtro)
        ->select('id','nombre','stock','precio_venta')
        ->where('stock','>','0')
        ->orderBy('nombre', 'asc')
        ->take(1)->get();

        return ['articulos' => $articulos];
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
        $articulo = new Articulo();
        $articulo->idcategoria = $request->idcategoria;
        $articulo->codigo = $request->codigo;
        $articulo->nombre = $request->nombre;
        $articulo->precio_venta = $request->precio_venta;
        $articulo->stock = $request->stock;
        $articulo->descripcion = $request->descripcion;
        $articulo->condicion = '1';
        $articulo->save();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if(!$request->ajax()) return redirect('/');
        $articulo = Articulo::findOrFail($request->id);
        $articulo->idcategoria = $request->idcategoria;
        $articulo->codigo = $request->codigo;
        $articulo->nombre = $request->nombre;
        $articulo->precio_venta = $request->precio_venta;
        $articulo->stock = $request->stock;
        $articulo->descripcion = $request->descripcion;
        $articulo->condicion = '1';
        $articulo->save();
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
        $articulo = Articulo::findOrFail($request->id);
        $articulo->condicion = '0';
        $articulo->save();
    }
    /**
     * Change condition's value to 1 (enable).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function activar(Request $request)
    {
        if(!$request->ajax()) return redirect('/');
        $articulo = Articulo::findOrFail($request->id);
        $articulo->condicion = '1';
        $articulo->save();
    }
}
