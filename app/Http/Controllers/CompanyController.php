<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Http\Traits\DatabaseTrait;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CompanyController extends Controller
{
    use DatabaseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $validSortFields = $this->getTableColumns('companies', ['deleted_at', 'updated_at']);

            $sorting = $this->getSortingParams($request, $validSortFields);

            $companies = Company::when($request->has('search') && $request->search != '', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orderBy($sorting['sort_field'], $sorting['sort_direction'])->paginate(10);

            return response()->json([
                'status' => true,
                'message' => 'Data Successfully Retrieved',
                'data' => $companies
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
    public function store(CompanyRequest $request): JsonResponse
    {
        $validatedReq = (object) $request->validated();

        try {
            DB::beginTransaction();

            $company = new Company();

            $company->name = $validatedReq->name;
            $company->email = $validatedReq->email;
            $company->phone_number = $validatedReq->phone_number;

            $company->save();

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

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $relations = [
                'employees',
            ];

            $company = Company::with($relations)->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Data Successfully Retrieved',
                'data' => $company
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
    public function update(CompanyRequest $request, string $id): JsonResponse
    {
        $validatedReq = (object) $request->all();

        try {
            DB::beginTransaction();

            $company =  Company::findOrFail($id);

            $company->name = $validatedReq->name;
            $company->email = $validatedReq->email;
            $company->phone_number = $validatedReq->phone_number;

            $company->save();

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

            Company::findOrFail($id)->delete();

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
