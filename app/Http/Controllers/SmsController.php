<?php

namespace App\Http\Controllers;

use App\Http\Resources\SmsResource;
use App\Http\Resources\ErrorResource;
use App\Repositories\SmsRepository;
use App\Enums\ErrorCode;
use App\Services\Sms\SmsErrors;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SmsController extends Controller
{
    public function __construct(private readonly SmsRepository $smsRepository) {}

    public function index(Request $request): JsonResponse
    {
        $limit = (int) ($request->query('limit', 50));
        if ($limit < 1) {
            $limit = 1;
        } elseif ($limit > 200) {
            $limit = 200;
        }

        $items = $this->smsRepository->fetchSent($limit);
        return new JsonResponse(
            array_map(fn ($sms) => SmsResource::make($sms)->toArray($request), $items),
            200
        );
    }
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'recipient_phone' => ['required', 'string', 'max:12', 'min:12', 'regex:/^90\d{10}$/'],
            'message' => ['required', 'string', 'max:200'],
        ]);

        $sms = $this->smsRepository->createPending(
            recipientPhone: $validated['recipient_phone'],
            message: $validated['message']
        );

        return SmsResource::make($sms)->response()->setStatusCode(201);
    }

    public function show(int $sms): JsonResponse
    {
        $found = $this->smsRepository->find($sms);
        if (!$found) {
            return ErrorResource::make([
                'code' => ErrorCode::NotFound->value,
                'message' => SmsErrors::message(ErrorCode::NotFound),
            ])->response()->setStatusCode(404);
        }

        return SmsResource::make($found)->response()->setStatusCode(200);
    }
}
