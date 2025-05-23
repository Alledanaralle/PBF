<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

header('Access-Control-Allow-Origin: *'); // Atau ganti * dengan URL Laravel Anda
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With, Authorization');
class User extends BaseController
{
    protected $model;
    use ResponseTrait;
    public function __construct()
    {
        $this->model = new UserModel();
    }
    public function index()
    {
        $data = $this->model->findAll();
        return $this->respond($data, 200);
    }

    public function show($id = null)
    {
        $data = $this->model->where("id_user", $id)->findAll();
        if ($data) {
            return $this->respond($data, 200);
        } else {
            return $this->failNotFound("Data tidak ditemukan");
        }
    }

    public function showName($nama = null)
{
    try {
        // Cek apakah parameter nama kosong
        if (empty($nama)) {
            return $this->fail('Nama user harus diisi', 400);
        }

        // Query untuk mencari data berdasarkan nama
        $data = $this->model->where('username', $nama)->findAll();

        // Cek apakah data ditemukan
        if (!empty($data)) {
            return $this->respond([
                'status' => 200,
                'message' => 'Data user ditemukan',
                'data' => $data
            ], 200);
        } else {
            return $this->failNotFound('Data user dengan nama ' . $nama . ' tidak ditemukan');
        }
    } catch (\Exception $e) {
        return $this->fail('Terjadi kesalahan: ' . $e->getMessage(), 500);
    }
}

    public function create()
    {
        $data = $this->request->getPost();
        
        if (!$this->model->save($data)) {
            return $this->fail($this->model->errors());
        }
        $response = [
            'status' => 200,
            'error' => null,
            'message' => [
                'success' => 'Berhasil Menambah Data',
            ]
        ];
        return $this->respond($response, 200);
    }

    public function update($id = null)
    {
        $data = $this->request->getRawInput();
        $data['id_user'] = $id;
        $ifExist = $this->model->where('id_user', $id)->findAll();
        if (!$ifExist) {
            return $this->failNotFound("Data tidak ditemukan");
        }

        if (!$this->model->save($data)) {
            return $this->fail($this->model->errors());
        }

        $response = [
            'status' => 200,
            'error' => null,
            'message' => [
                'success' => 'Berhasil Mengubah Data',
            ]
        ];
        return $this->respond($response, 200);
    }

    public function delete($id = null)
    {
        $data = $this->model->where('id_user', $id)->findAll();
        if ($data) {
            $this->model->delete($id);
            $response = [
                'status' => 200,
                'error' => null,
                'message' => [
                    'success' => 'Berhasil Menghapus Data',
                ]
            ];
            return $this->respondDeleted($response);
        } else {
            return $this->failNotFound("Data tidak ditemukan");
        }
    }
}
