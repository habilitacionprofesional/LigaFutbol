<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Car;

class CarController extends Controller{
    
    public function index(Request $Request){
        $cars = Car::all()->load('user');
        return response()->json(array(
                'cars' => $cars,
                'status' => 'success'
        ), 200);
    }

    public function show($id){
        $car = Car::find($id);
        if(is_object($car)){
            $car = Car::find($id)->load('user');
            return response()->json(array(
                'car' => $car,
                'status' => 'success'
            ),200);
        }else{
            return response()->json(array(
                'message' => 'El coche no existe',
                'status' => 'error'
            ),200);    
        }
       
        
    }

    public function store(Request $Request){
        $hash = $Request->header('Authorization', null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){
            // Recoger datos por POST
            $json = $Request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);

            // Conseguir el usuario identificado
            $user = $jwtAuth->checkToken($hash, true);
            
            // Validación
            $validate = \Validator::make($params_array, [
                'title' => 'required|min:5',
                'description' => 'required',
                'price' => 'required',
                'status' => 'required'
            ]);

            if($validate->fails()){
                return response()->json($validate->errors(), 400);
            }
 
            // Guardar el coche
            if(isset($params->title) && isset($params->description) && isset($params->price) && isset($params->status)){
                $car = new Car();
                $car->user_id = $user->sub;
                $car->title = $params->title;
                $car->description = $params->description;
                $car->price = $params->price;
                $car->status = $params->status; 

                $car->save();

                $data = array(
                    'car' => $car,
                    'status' => 'success',
                    'code'  => 200
                );
            }
        }else{
            // Devolver error
            $data = array(
                'message' => 'Login incorrecto',
                'status' => 'error',
                'code'  => 300
            );
        }

        return response()->json($data, 200);  
    }

    public function update($id, Request $request){
        $hash = $request->header('Authorization', null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);
       
        if($checkToken){     // Recoger Parámetro por POST
            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);

            $validate = \Validator::make($params_array, [    // Validar datos
                'title' => 'required|min:5',
                'description' => 'required',
                'price' => 'required',
                'status' => 'required'
            ]);
            if($validate->fails()){
                return response()->json($validate->errors(), 400);
            }
            // Actualizo el registro
            unset($params_array['id']);
            unset($params_array['user_id']);
            unset($params_array['created_at']);
            unset($params_array['updated_at']);
            unset($params_array['user']);
            // var_dump($params_array);  die();

            $car = Car::where('id', $id)->update($params_array);
            $data = array(
                'car' => $params,
                'status' => 'success',
                'code' => 200
            );
        }else{
            // Devolver error
            $data = array(
                'message' => 'Login incorrecto',
                'status' => 'error',
                'code'  => 300
            );
        }

        return response()->json($data, 200);  
    }

    public function destroy($id, Request $request){
        $hash = $request->header('Authorization', null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){
            // Comprobar que exista el usuario
            $car = Car::find($id);

            $car->delete();    // Borrarlo

            $data = array(     // Devolverlo
                'car'    => $car,
                'status' => 'success',
                'code'   => 200
            );
        } else{
            $data = array(
                'status'  => 'error',
                'code'    => 400,
                'message' => 'Login Incorrecto'
            );
        }

        return response()->json($data, 200);

    }

} // end class



