<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class EmpleadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $datos['empleados']=Empleado::paginate(1);
        return view('empleado.index',$datos);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('empleado.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //$datosEmpleado=request()->all();

        $campos=[
            'Nombre'=>'required|string|max:100',
            'ApellidoPaterno'=>'required|string|max:100',
            'ApellidoMaterno'=>'required|string|max:100',
            'Correo'=>'required|email',
            'Foto'=>'required|max:10000|mimes:jpeg,png,jpg'
        ];

        $mensaje=[
            'required'=>'El :attribute es requerido',
            'Foto.required'=>'La Foto es requerida'
        ];

        $this->validate($request,$campos,$mensaje);

        $datosEmpleado=request()->except('_token');
       //Preguntamos si la foto existe y si es así la almacenamos en "storage":
        if($request->hasFile('Foto')){
            $datosEmpleado['Foto']=$request->file('Foto')->store('uploads','public');
        }

        Empleado::insert($datosEmpleado);
        /*return response()->json($datosEmpleado); //Muestra los datos insertados en formato Json.*/
        return redirect('empleado')->with('mensaje','Empleado agregado con éxito');

    }


    /**
     * Display the specified resource.
     */
    public function show(Empleado $empleado)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //Almaceno en $empleado toda la información del registro con identificador "$id"
        //Estamos haciendo uso del modelo "Empleado::"
        $empleado=Empleado::findOrFail($id);

        //Llamo a la vista pasándole toda esa información almacenada en $empleado.
        return view('empleado.edit',compact('empleado'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $campos=[
            'Nombre'=>'required|string|max:100',
            'ApellidoPaterno'=>'required|string|max:100',
            'ApellidoMaterno'=>'required|string|max:100',
            'Correo'=>'required|email',
            
        ];

        $mensaje=[
            'required'=>'El :attribute es requerido',
            
        ];

        if($request->hasFile('Foto')){
            $campos=['Foto'=>'required|max:10000|mimes:jpeg,png,jpg'];
            $mensaje=['Foto.required'=>'La foto es requerida'];
        }

        $this->validate($request,$campos,$mensaje);



        //Lo cogemos todo menos el token y el método. Es decir, todo menos:
        //@csrf   y    {{method_field('PATCH') }} que pusimos en el formulario de "edit.blade.php"
        $datosEmpleado=request()->except(['_token','_method']);

        //Comprobamos si la foto existe, y si es así, borramos la que teníamos y almacenamos la nueva:
        if($request->hasFile('Foto')){
            $empleado=Empleado::findOrFail($id); //Cogemos la información del empleado en cuestión.

            Storage::delete('public/'.$empleado->Foto);//Borramos la foto que haya de dicho empleado en el Storage.

            $datosEmpleado['Foto']=$request->file('Foto')->store('uploads','public'); //Guardamos la nueva foto en Storage.
        }

        //Utilizamos el modelo "Empleado" para hacer la actualización en la base de datos.
        //Para ello le decimos que busque aquel registro que tenga el $id que le pasamos y 
        //si lo encuentra que actualice la BD con todos los datos que hay en $datosEmpleado
        Empleado::where('id','=',$id)->update($datosEmpleado);

        //Que por último retorne al formulario que me envió la información, pero ya con 
        //la información actualizada:
        $empleado=Empleado::findOrFail($id);
        //return view('empleado.edit',compact('empleado'));
        
        return redirect('empleado')->with('mensaje','Empleado Modificado');
    }

    /**
     * Remove the specified resource from storage.
     */

     
    public function destroy($id)
    {
        $empleado=Empleado::findOrFail($id);
        
        if(Storage::delete('public/'.$empleado->Foto)){
            Empleado::destroy($id);
        }
        
        return redirect('empleado')->with('mensaje','Empleado Borrado');
        
    }
}

