<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Proveedor;
use App\Persona;

class ProveedorController extends Controller
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
            $personas = Proveedor::join('persona as per', 'proveedor.id', 'per.id')
            ->select('per.id', 'per.nombre', 'per.tipo_documento', 'per.num_documento','per.direccion', 
            'per.telefono', 'per.email', 'proveedor.contacto', 'proveedor.telefono_contacto')
            ->orderBy('per.id', 'desc')
            ->paginate(10);
        }else{
            $personas = Proveedor::join('persona as per', 'proveedor.id', 'per.id')
            ->select('per.id', 'per.nombre', 'per.tipo_documento', 'per.num_documento', 'per.direccion', 
            'per.telefono', 'per.email', 'proveedor.contacto', 'proveedor.telefono_contacto')
            ->where('per'.$criterio, 'like', '%'.$buscar.'%')
            ->orderBy('per.id', 'desc')
            ->paginate(10);
        }
        return [
            'pagination' => [
                'total' => $personas->total(),
                'current_page' => $personas->currentPage(),
                'per_page' => $personas->perPage(),
                'last_page' => $personas->lastPage(),
                'from' => $personas->firstItem(),
                'to' => $personas->lastItem()
            ],
            'personas' => $personas
        ];
    }

    /**
     * Return row proveedores.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function selectProveedor(Request $request){
        if (!$request->ajax()) return redirect('/');
        
        $filtro = $request->filtro;
        $proveedores = Proveedor::join('persona as per','proveedor.id','=','per.id')
        // ->where('per.nombre', 'like', '%'. $filtro . '%')
        // ->orWhere('per.num_documento', 'like', '%'. $filtro . '%')
        // ->select('per.id','per.nombre','per.num_documento')
        ->select('per.id','per.nombre')
        ->orderBy('per.nombre', 'asc')->get();

        return ['proveedores' => $proveedores];
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
            $persona = new Persona();
            $persona->nombre = $request->nombre;
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = $request->num_documento;
            $persona->direccion = $request->direccion;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;
            $persona->save();

            $proveedor = new Proveedor();
            $proveedor->contacto = $request->contacto;
            $proveedor->telefono_contacto = $request->telefono_contacto;
            $proveedor->id = $persona->id;
            $proveedor->save();

            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
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
        try{
            DB::beginTransaction();
            $proveedor = Proveedor::findOrFail($request->id);
            $persona = Persona::findOrFail($proveedor->id);
            $persona->nombre = $request->nombre;
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = $request->num_documento;
            $persona->direccion = $request->direccion;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;
            $persona->save();

            $proveedor->contacto = $request->contacto;
            $proveedor->telefono_contacto = $request->telefono_contacto;
            $proveedor->save();

            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
    }
}
