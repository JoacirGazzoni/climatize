<?php

namespace App\Http\Controllers;

use App\Models\Freezer;
use App\Models\ClienteNovo;
use App\Models\StatusSensor;
use Illuminate\Http\Request;

class FreezersController extends Controller
{
    private $errorMessages = [
        'cad_cliente_id.required' => 'O campo Status Sensor é obrigatório.',
        'status_sensor_id.required' => 'O campo Status Sensor é obrigatório.',
        'nome_unidade.required' => 'O campo Nome Unidade é obrigatório.',
        'referencia.required' => 'O campo Referência é obrigatório.',
        'setpoint.required' => 'O campo Setpoint é obrigatório.',
        'limite_neg.required' => 'O campo Limite Neg é obrigatório.',
        'limite_pos.required' => 'O campo Limite Pos é obrigatório.'
    ];

    private function validate_params(Request $request)
    {
        return $request->validate([
            'status_sensor_id' => 'required|exists:status_sensors,id',
            'cad_cliente_id' => 'required|exists:cad_clientes,id',
            'nome_unidade' => 'required',
            'referencia' => 'required',
            'setpoint' => 'required',
            'limite_neg' => 'required',
            'limite_pos' => 'required',
            
            'etiqueta_ident' => 'nullable|string',
            'detalhe' => 'nullable|string|max:500'
        ], $this->errorMessages);
    }

    public function index(Request $request)
    {
        try {
            $query = Freezer::query();

            if (auth()->check() && auth()->user()->email === 'rodrigo@4climatize.com.br') {
                $query->where('cad_cliente_id', '3');
            } else {
                if ($request->filled('search')) {
                    $search = $request->input('search');
                    $query->where(function ($subquery) use ($search) {
                        $subquery->where('id_equipamento', 'like', "%{$search}%")
                            ->orWhere('referencia', 'like', "%{$search}%")
                            ->orWhere('detalhe', 'like', "%{$search}%")
                            ->orWhere('etiqueta_ident', 'like', "%{$search}%");
                    });
                }
            }

            $freezers = $query->get();

            return view('freezers.index', ['freezers' => $freezers]);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao listar os freezers: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $all_clients = ClienteNovo::all();
            $status_sensors = StatusSensor::whereDoesntHave('freezer')
                                        ->where('status', '!=', 'I')
                                        ->where('status', '!=', 'A')
                                        ->get();
            return view('freezers.create', compact('all_clients', 'status_sensors'));
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao exibir o formulário de criação: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $this->validate_params($request);
            Freezer::create($validatedData);

            return redirect()->route('freezers')->with('success', 'Freezer criado com sucesso!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao criar o freezer: ' . $e->getMessage())->withInput();
        }
    }


    public function edit($id)
    {
        try {
            $all_clients = ClienteNovo::all();
            $status_sensors = StatusSensor::whereDoesntHave('freezer')
                                          ->where('status', '!=', 'I')
                                          ->where('status', '!=', 'A')
                                          ->get();
            $freezer = Freezer::findOrFail($id);

            return view('freezers.edit', compact('freezer', 'all_clients', 'status_sensors'));
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao exibir o formulário de edição: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $this->validate_params($request);

            $freezer = Freezer::findOrFail($id);
            $freezer->update($validatedData);

            return redirect()->route('freezers')->with('success', 'Freezer atualizado com sucesso!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao atualizar o freezer: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $freezer = Freezer::findOrFail($id);
            $freezer->delete();
            
            return redirect()->route('freezers')->with('success', 'Freezer excluído com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao excluir o freezer: ' . $e->getMessage());
        }
    }    
}
