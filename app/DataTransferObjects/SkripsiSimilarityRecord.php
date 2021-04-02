<?php


namespace App\DataTransferObjects;


use App\Models\Skripsi;
use Illuminate\Support\Collection;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Reflection;

class SkripsiSimilarityRecord extends DataTransferObject
{
    public Skripsi $skripsi;
    /** @var Collection | KalimatSimilarityRecord[] */
    public Collection $mostSimilarKalimats;
    public float $chebyshevDistanceAverage;
    public float $diceSimilarityAverage;
}