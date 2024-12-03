<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Http\Traits\DatabaseTrait;
use App\Models\Employee;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    use DatabaseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $validSortFields = $this->getTableColumns('employees', ['deleted_at', 'updated_at']);

            $sorting = $this->getSortingParams($request, $validSortFields);

            if (in_array(Auth::user()->role, ["Manager", "Super Admin"])) {
                $employees = User::leftJoin('employees', 'users.id', '=', 'employees.user_id')
                    ->select(
                        'users.id',
                        'users.email',
                        'users.role',
                        'employees.name',
                        'employees.phone_number',
                        'employees.address',
                        'users.created_at'
                    )
                    ->when($request->has('search') && $request->search != '', function ($q) use ($request) {
                        $q->whereHas('employee', function ($q) use ($request) {
                            $q->where('name', 'like', '%' . $request->search . '%');
                        });
                    })
                    ->when(Auth::user()->role == "Manager", function ($q) {
                        $q->whereIn('role', ["Manager", "Employee"]);
                    })
                    ->orderBy('employees.' . $sorting['sort_field'], $sorting['sort_direction'])
                    ->paginate(10);
            }

            if (Auth::user()->role == "Employee") {
                $employees = Employee::when($request->has('search') && $request->search != '', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                })
                    ->where('company_id', Auth::user()->company_id)
                    ->orderBy($sorting['sort_field'], $sorting['sort_direction'])
                    ->paginate(10);
            }

            return response()->json([
                'status' => true,
                'message' => 'Data Successfully Retrieved',
                'data' => $employees ?? []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeRequest $request): JsonResponse
    {
        $validatedReq = (object) $request->validated();

        try {
            DB::beginTransaction();

            $userId = $this->storeEmployeeUser($validatedReq,);

            $employee = new Employee();

            $employee->user_id = $userId;
            $employee->name = $validatedReq->name;
            $employee->phone_number = $validatedReq->phone_number;
            $employee->address = $validatedReq->address;
            $employee->company_id = Auth::user()->company_id;

            $employee->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "Data has been saved"
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    private function storeEmployeeUser($request)
    {
        try {
            $user = new User();

            $user->name = $request->name;
            $user->password = bcrypt('password123');
            $user->email = $request->email;
            $user->role = 'Employee';
            $user->company_id = Auth::user()->company_id;

            $user->save();

            return $user->id;
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $relation = [
                'employee:id,user_id,name,phone_number,address',
                'company:id,name,email'
            ];

            $user = User::select('id', 'name', 'role', 'company_id')
                ->when(Auth::user()->role == "Employee", function ($q) {
                    $q->where('role', '=', 'Employee');
                })
                ->with($relation)->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Data Successfully Retrieved',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeRequest $request, string $id): JsonResponse
    {
        $validatedReq = (object) $request->all();


        try {
            DB::beginTransaction();

            $employee = Employee::where('user_id', $id)->first();

            if (!$employee) {
                throw new Exception('Data Not Found');
            }

            $employee->name = $validatedReq->name;
            $employee->phone_number = $validatedReq->phone_number;
            $employee->address = $validatedReq->address;

            $employee->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data Has Been Updated'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            Employee::where('user_id', $id)->delete();
            User::findOrFail($id)->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data Has Been Deleted'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }
}
