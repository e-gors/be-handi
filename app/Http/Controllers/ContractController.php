<?php

namespace App\Http\Controllers;

use Exception;
use App\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ContractResource;

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
                    $query->whereHas('post', function ($query) use ($type) {
                        $query->where('job_type', $type);
                    });
                }
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

            $query->with(['post.user', 'bid.user', 'offer.user']);

            $query->where(function ($query) use ($user, $search) {
                $query->whereHas('post.user', function ($query) use ($user, $search) {
                    $query->where('id', $user->id)
                        ->where('first_name', 'LIKE', "%$search%")
                        ->orWhere('last_name', 'LIKE', "%$search%")
                        ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%$search%");
                })
                    ->orWhereHas('bid.user', function ($query) use ($user, $search) {
                        $query->where('id', $user->id)
                            ->where('first_name', 'LIKE', "%$search%")
                            ->orWhere('last_name', 'LIKE', "%$search%")
                            ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%$search%");
                    })
                    ->orWhereHas('offer.user', function ($query) use ($user, $search) {
                        $query->where('id', $user->id)
                            ->where('first_name', 'LIKE', "%$search%")
                            ->orWhere('last_name', 'LIKE', "%$search%")
                            ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%$search%");
                    });
            })
                ->orWhereHas('offer', function ($query) use ($user, $search) {
                    $query->whereHas('user', function ($query) use ($user, $search) {
                        $query->where('id', $user->id)
                            ->where('first_name', 'LIKE', "%$search%")
                            ->orWhere('last_name', 'LIKE', "%$search%")
                            ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%$search%");
                    });
                });

            // dd($query->toSql());

            return ContractResource::collection($this->paginated($query, $request));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
