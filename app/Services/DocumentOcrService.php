<?php

namespace App\Services;

use thiagoalessio\TesseractOCR\TesseractOCR;
use thiagoalessio\TesseractOCR\TesseractOcrException;

class DocumentOcrService
{
    private const MIN_READABLE_CHARACTERS = 20;

    /**
     * Heuristic check: does this file contain enough recognizable text to
     * plausibly be a real ID document photo? This is not real identity
     * verification — it only catches blank, corrupt, or non-document images.
     */
    public function looksLikeReadableDocument(string $absolutePath): bool
    {
        if (str_ends_with(strtolower($absolutePath), '.pdf')) {
            return true;
        }

        try {
            $text = $this->extractText($absolutePath);
        } catch (TesseractOcrException) {
            return false;
        }

        return mb_strlen(trim($text)) >= self::MIN_READABLE_CHARACTERS;
    }

    public function extractText(string $absolutePath): string
    {
        return (new TesseractOCR($absolutePath))
            ->executable(config('services.tesseract.binary'))
            ->run();
    }
}
