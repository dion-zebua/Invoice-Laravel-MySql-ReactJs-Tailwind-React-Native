<?php

namespace App\Traits;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

trait BaseResponse
{

    protected function errorResponse($message = 'Maaf Ada yang Salah.')
    {
        return response()->json([
            'status'  => false,
            'message' => $message,
        ], 500);
    }

    protected function success($text = 'Berhasil Dijalankan.')
    {
        return response()->json([
            'status' => true,
            'message' => $text,
        ], 200);
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => $validator->errors(),
        ], 422));
    }

    protected function unauthorizedResponse($text = 'Akses tidak sah.')
    {
        return response()->json([
            'status' => false,
            'message' => $text,
        ], 403);
    }

    protected function dataNotFound($text = NULL)
    {
        return response()->json([
            'status' => false,
            'message' => ($text ?? 'Data') . ' tidak ditemukan.'
        ], 404);
    }

    protected function dataFound($data = NULL, $text = Null)
    {
        return response()->json([
            'status' => true,
            'message' => ($text ?? 'Data') . ' ditemukan.',
            'data' => $data,
        ], 200);
    }

    protected function createSuccess($data)
    {
        return response()->json([
            'status' => true,
            'message' => 'Berhasil tambah.',
            'data' => $data,
        ], 201);
    }

    protected function editSuccess($data)
    {
        return response()->json([
            'status' => true,
            'message' => 'Berhasil edit.',
            'data' => $data,
        ], 200);
    }

    protected function deleteSuccess()
    {
        return response()->json([
            'status' => true,
            'message' => 'Berhasil hapus.',
        ], 200);
    }

    protected function limitTime($text, $time)
    {
        return response()->json([
            'status' => false,
            'message' => "Kirim ulang $text setelah $time.",
        ], 429);
    }

    protected function tokenExpired()
    {
        return response()->json([
            'status' => false,
            'message' => "Token kadaluarsa.",
        ], 400);
    }
}
