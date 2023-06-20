<?php

namespace App\Http\Controllers;

use Exception;
use App\Contract;
use App\Http\Resources\ClientResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ContractResource;
use App\Http\Resources\WorkerResource;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->search ? $request->search : null;
            $type = $request->type ? $request->type : null;
            $status = $request->status ? $request->status : null;
            $orderByRate = $request->order_by_rate ? $request->order_by_rate : null;
            $orderByDate = $request->order_by_date ? $request->order_by_date : null;

            $user = auth()->user();
            $query = Contract::query();

            if ($type) {
                if ($type !== 'all') {
                    $query->whereHas('post', function ($query) use ($type, $user) {
                        $query->where('job_type', $type);
                    });
                }
            }

            if ($user->role === 'Client') {
                $query->whereHas('post', function ($query) use ($type, $user) {
                    $query->where('user_id', $user->id);
                });
            } else {
                $query->where(function ($query) use ($user) {
                    $query->whereHas('bid', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })->orWhereHas('offer', function ($query) use ($user) {
                        $query->join('users', 'offers.profile_id', '=', 'users.id')
                            ->where('users.id', $user->id);
                    });
                });
            }

            if ($status) {
                $query->where('status', $status);
            }
            if ($orderByRate) {
                $query->whereHas('post', function ($query) use ($orderByRate) {
                    $query->orderBy('rate', $orderByRate === 'asc' ? 'asc' : 'desc');
                });
            }
            if ($orderByDate) {
                $query->orderBy('created_at', $orderByDate === 'asc' ? 'asc' : 'desc');
            }

            if (!is_null($search)) {
                $query->where(function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->whereHas('post.user', function ($query) use ($search) {
                            $query->where(function ($query) use ($search) {
                                $query->where('users.first_name', 'LIKE', "%$search%")
                                    ->orWhere('users.last_name', 'LIKE', "%$search%")
                                    ->orWhere(DB::raw("CONCAT(users.first_name, ' ', users.last_name)"), 'LIKE', "%$search%");
                            });
                        });
                    })
                        ->orWhere(function ($query) use ($search) {
                            $query->whereHas('bid.user', function ($query) use ($search) {
                                $query->where(function ($query) use ($search) {
                                    $query->where('users.first_name', 'LIKE', "%$search%")
                                        ->orWhere('users.last_name', 'LIKE', "%$search%")
                                        ->orWhere(DB::raw("CONCAT(users.first_name, ' ', users.last_name)"), 'LIKE', "%$search%");
                                });
                            });
                        })
                        ->orWhereHas('offer.worker', function ($query) use ($search) {
                            $query->where(function ($query) use ($search) {
                                $query->where('users.first_name', 'LIKE', "%$search%")
                                    ->orWhere('users.last_name', 'LIKE', "%$search%")
                                    ->orWhere(DB::raw("CONCAT(users.first_name, ' ', users.last_name)"), 'LIKE', "%$search%");
                            });
                        });
                })->orWhereHas('post', function ($query) use ($search) {
                    $query->where('title', 'LIKE', "%$search%")
                        ->orWhere('position', 'LIKE', "%$search%");
                });
            }


            return ContractResource::collection($this->paginated($query, $request));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function completed(Contract $contract)
    {
        $user = auth()->user();

        if ($contract) {
            if ($contract->status !== 'in progress') {
                return response()->json([
                    'code' => 500,
                    'message' => 'The contract is already completed or still on pending!',
                ]);
            } else {
                $contract->update([
                    'status' => 'completed'
                ]);

                return response()->json([
                    'code' => 200,
                    'message' => 'Your contract is completed!',
                    'user' => $user->role === 'Client' ? new ClientResource($user) : new WorkerResource($user)
                ]);
            }
        } else {
            return response()->json([
                'code' => 404,
                'message' => "Contract not found!"
            ]);
        }
    }
}
