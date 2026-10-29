<?php

namespace App\Controllers;

use App\Models\ShiftModel;
use App\Models\ExpenseModel;
use App\Models\SalesModel; // Asumsi Anda sudah punya SalesModel

class ShiftController extends BaseController
{
    protected $shiftModel;
    protected $expenseModel;
    protected $salesModel;

    public function __construct()
    {
        $this->shiftModel = new ShiftModel();
        $this->expenseModel = new ExpenseModel();
        $this->salesModel = new SalesModel();
    }

    public function index()
    {
        $userId = session()->get('id'); // Pastikan session user_id tersedia
        $activeShift = $this->shiftModel->getActiveShift($userId);

        if (!$activeShift) {
            return view('shift/open_shift_view'); // Tampilkan form modal awal
        }

        // Jika ada shift aktif, ambil data ringkasan sementara
        $data['shift'] = $activeShift;
        $data['total_sales'] = $this->salesModel->where('shift_id', $activeShift['id'])->where('payment_method', 'Tunai')->selectSum('total_price')->first()['total_price'] ?? 0;
        $data['total_expenses'] = $this->expenseModel->where('shift_id', $activeShift['id'])->selectSum('nominal')->first()['nominal'] ?? 0;
        
        return view('shift/active_shift_view', $data);
    }

    // PROSES BUKA SHIFT
    public function open()
    {
        $startingCash = $this->request->getPost('starting_cash');

        $this->shiftModel->insert([
            'user_id'       => session()->get('id'),
            'start_at'      => date('Y-m-d H:i:s'),
            'starting_cash' => $startingCash,
            'status'        => 'open'
        ]);

        return redirect()->to('/dashboard')->with('success', 'Shift berhasil dibuka.');
    }

    // PROSES TUTUP SHIFT
    public function close()
{
    // 1. Ambil ID dari session (Pastikan kuncinya 'id')
    $userId = session()->get('id'); 
    $activeShift = $this->shiftModel->getActiveShift($userId);

    // Debugging: Jika tampilan tetap sama, hapus tanda komentar di bawah ini untuk cek data
    // dd($activeShift); 

    if (!$activeShift) {
        return redirect()->to(base_url('shift'))->with('error', 'Shift tidak ditemukan.');
    }

    $actualCash = $this->request->getPost('actual_cash');

    $totalSales = $this->salesModel->where('shift_id', $activeShift['id'])
                                   ->where('payment_method', 'Tunai')
                                   ->selectSum('total_price')
                                   ->first()['total_price'] ?? 0;

    $totalExpenses = $this->expenseModel->where('shift_id', $activeShift['id'])
                                        ->selectSum('nominal')
                                        ->first()['nominal'] ?? 0;

    $expectedCash = ($activeShift['starting_cash'] + $totalSales) - $totalExpenses;

    // 2. PROSES UPDATE DATABASE
    $this->shiftModel->update($activeShift['id'], [
        'end_at'           => date('Y-m-d H:i:s'),
        'total_sales_cash' => $totalSales,
        'total_expenses'   => $totalExpenses,
        'expected_cash'    => $expectedCash,
        'actual_cash'      => $actualCash,
        'status'           => 'closed' // PENTING: Status harus jadi closed
    ]);

    // 3. REDIRECT KEMBALI KE INDEX SHIFT
    // Karena status sudah 'closed', maka index() akan otomatis menampilkan 'open_shift_view'
    $saleId = $activeShift['id'];
    return redirect()->to(base_url('shift/summary/' . $saleId))->with('success', 'Shift ditutup.');
    }

    // Tambah pengeluaran kas kecil saat shift berjalan
    public function addExpense()
    {
        $userId = session()->get('id');
        $activeShift = $this->shiftModel->getActiveShift($userId);

        $this->expenseModel->insert([
            'shift_id'   => $activeShift['id'],
            'nominal'    => $this->request->getPost('nominal'),
            'keterangan' => $this->request->getPost('keterangan'),
        ]);

        return redirect()->back()->with('success', 'Pengeluaran dicatat.');
    }

    public function summary($id)
{
    // Mengambil data shift yang baru saja ditutup
    $shift = $this->shiftModel->find($id);
    
    if (!$shift) {
        return redirect()->to('/shift')->with('error', 'Data tidak ditemukan.');
    }

    $data = [
        'title' => 'Rekap Penutupan Kasir',
        'shift' => $shift,
        // Hitung selisih lagi untuk memastikan tampilan benar
        'selisih' => $shift['actual_cash'] - $shift['expected_cash']
    ];

    return view('shift/summary_view', $data);
}

public function history()
{
    $data = [
        'title' => 'Riwayat Shift Kasir',
        // Kita join dengan tabel users untuk tahu nama kasirnya
        'shifts' => $this->shiftModel->select('shifts.*, users.full_name')
                        ->join('users', 'users.id = shifts.user_id')
                        ->orderBy('shifts.id', 'DESC')
                        ->findAll()
    ];

    return view('shift/history_view', $data);
}
}