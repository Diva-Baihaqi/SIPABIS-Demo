<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    protected int $stokTersedia;
    protected int $jumlahDiminta;

    public function __construct(string $message = "Stok barang tidak mencukupi untuk transaksi ini.", int $stokTersedia = 0, int $jumlahDiminta = 0, int $code = 422, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->stokTersedia = $stokTersedia;
        $this->jumlahDiminta = $jumlahDiminta;
    }

    public function getStokTersedia(): int
    {
        return $this->stokTersedia;
    }

    public function getJumlahDiminta(): int
    {
        return $this->jumlahDiminta;
    }
}
