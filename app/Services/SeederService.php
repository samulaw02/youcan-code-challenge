<?php

namespace App\Services;


class SeederService
{
    /**
     * Generate random product data.
     *
     * @return array
     */
    public function generateRandomProduct()
    {
        return [
            'name' => $this->generateRandomWord(3, 5),
            'description' => $this->generateRandomParagraph(),
            'price' => $this->generateRandomFloat(1, 1000),
            'image' => $this->generateRandomImageUrl(),
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Generate a random word.
     *
     * @param int $min
     * @param int $max
     * @return string
     */
    public function generateRandomWord($min, $max)
    {
        $length = rand($min, $max);
        $characters = implode('', array_merge(range('a', 'z'), range('A', 'Z')));
        $word = '';

        // Use array for faster string concatenation
        $charsArray = str_split($characters);

        for ($i = 0; $i < $length; $i++) {
            $word .= $charsArray[array_rand($charsArray)];
        }

        return $word;
    }

    /**
     * Generate a random paragraph.
     *
     * @return string
     */
    public function generateRandomParagraph()
    {
        $sentences = [];

        for ($i = 0; $i < rand(1, 3); $i++) {
            $sentences[] = $this->generateRandomSentence();
        }

        return implode(' ', $sentences);
    }

    /**
     * Generate a random sentence.
     *
     * @return string
     */
    public function generateRandomSentence()
    {
        $words = [];

        for ($i = 0; $i < rand(2, 4); $i++) {
            $words[] = $this->generateRandomWord(2, 3);
        }

        return ucfirst(implode(' ', $words)) . '.';
    }

    /**
     * Generate a random float.
     *
     * @param float $min
     * @param float $max
     * @return float
     */
    public function generateRandomFloat($min, $max)
    {
        return round(($min + lcg_value() * (abs($max - $min))), 2);
    }

    /**
     * Generate a random image URL.
     *
     * @return string
     */
    public function generateRandomImageUrl()
    {
        $imageNumber = rand(1, 10); // Assuming you have 10 placeholder images
        return "https://via.placeholder.com/640x480.png/00{$imageNumber}ee?text=image";
    }


    /**
     * Generate random category data.
     *
     * @return array
     */
    public function generateCategory($counter)
    {
        return [
            'name' => 'Category ' . $counter,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
