<?php


namespace App\DataTransferObjects;


use App\Models\KalimatSkripsi;
use Spatie\DataTransferObject\DataTransferObject;

class KalimatSimilarityRecord extends DataTransferObject
{
    public int $kalimatAId;
    public int $kalimatBId;
    public float $chebyshevDistance;
    public float $diceSimilarity;
}