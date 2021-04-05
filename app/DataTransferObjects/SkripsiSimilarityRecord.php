<?php


namespace App\DataTransferObjects;


use App\Models\KalimatSkripsi;
use App\Models\Skripsi;
use Spatie\DataTransferObject\DataTransferObject;

class SkripsiSimilarityRecord extends DataTransferObject
{
    public Skripsi $skripsi;
    public float $avgDiceSimilarity;
    public ?float $avgChebyshevDistance;
}