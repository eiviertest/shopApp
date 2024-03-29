<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Persona;

class ClienteController extends Controller
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
            $personas = Persona::orderBy('id', 'desc')->paginate(10);
        }else{
            $personas = Persona::where($criterio, 'like', '%'.$buscar.'%')->orderBy('id', 'desc')->paginate(10);
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

    public function selectCliente(Request $request){
        if (!$request->ajax()) return redirect('/');

        // $filtro = $request->filtro;
        $clientes = Persona::orderBy('nombre', 'asc')->get();
        // ::where('nombre', 'like', '%'. $filtro . '%')
        // ->orWhere('num_documento', 'like', '%'. $filtro . '%')
        // ->select('id','nombre','num_documento')

        return ['clientes' => $clientes];
    }

    public function listarPdf(){
        $personas = Persona::orderBy('persona.nombre', 'asc')->get();        
        $cont=Persona::count();

        $pdf = \PDF::loadView('pdf.clientespdf',['clientes'=>$personas,'cont'=>$cont])->setPaper('a4', 'portrait');
        return $pdf->download('clientes.pdf');
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
        $persona = new Persona();
        $persona->nombre = $request->nombre;
        $persona->tipo_documento = $request->tipo_documento;
        $persona->num_documento = $request->num_documento;
        $persona->direccion = $request->direccion;
        $persona->telefono = $request->telefono;
        $persona->email = $request->email;
        $persona->save();
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
        $persona = Persona::findOrFail($request->id);
        $persona->nombre = $request->nombre;
        $persona->tipo_documento = $request->tipo_documento;
        $persona->num_documento = $request->num_documento;
        $persona->direccion = $request->direccion;
        $persona->telefono = $request->telefono;
        $persona->email = $request->email;
        $persona->save();
    }
}
