<?php

namespace App\Support;

class UploadLimit
{
    /**
     * Taille maximale (en Ko) qu'un fichier envoyé peut réellement atteindre sur ce
     * serveur : le plus petit des réglages PHP upload_max_filesize et post_max_size.
     * Au-delà, PHP tronque la requête avant même que Laravel ne s'exécute.
     */
    public static function maxKilobytes(): int
    {
        $upload = self::iniToBytes((string) ini_get('upload_max_filesize'));
        $post = self::iniToBytes((string) ini_get('post_max_size'));

        return (int) floor(min($upload, $post) / 1024);
    }

    public static function maxMegabytes(): float
    {
        return round(self::maxKilobytes() / 1024, 1);
    }

    /**
     * Libellé humain, ex: "40 Mo" ou "2,5 Mo".
     */
    public static function label(): string
    {
        return self::formatMb(self::maxMegabytes());
    }

    /**
     * Limite (en Ko) réellement utilisée dans les règles de validation métier.
     *
     * Volontairement inférieure à maxKilobytes() : si un fichier dépasse
     * upload_max_filesize/post_max_size, PHP rejette la requête avant même que
     * Laravel ne démarre, et aucun message d'erreur propre ne peut être affiché.
     * Garder une marge sous la limite serveur laisse à la validation Laravel
     * une chance réelle d'intercepter le fichier trop volumineux avant ce mur.
     */
    public static function recommendedKilobytes(): int
    {
        return (int) floor(self::maxKilobytes() * 0.5);
    }

    public static function recommendedLabel(): string
    {
        return self::formatMb(round(self::recommendedKilobytes() / 1024, 1));
    }

    /**
     * Construit des règles de validation Laravel (+ messages) pour un lot de
     * champs fichiers nommés dynamiquement (ex: "piece_12", "document_3").
     *
     * @param  iterable  $items  Collection d'objets avec un id et un libellé (ex: Piecetype::all())
     */
    public static function dynamicFileRules(iterable $items, string $prefix, string $labelKey = 'titre'): array
    {
        $maxKb = self::recommendedKilobytes();
        $label = self::recommendedLabel();

        $rules = [];
        $messages = [];

        foreach ($items as $item) {
            $field = $prefix . $item->id;
            $rules[$field] = "nullable|file|max:{$maxKb}";
            $messages["{$field}.max"] = "Le fichier \"{$item->{$labelKey}}\" ne doit pas dépasser {$label}.";
        }

        return [$rules, $messages];
    }

    private static function formatMb(float $mb): string
    {
        return (floor($mb) == $mb ? (string) (int) $mb : number_format($mb, 1, ',', '')) . ' Mo';
    }

    private static function iniToBytes(string $value): int
    {
        $value = trim($value);

        if ($value === '' || $value === '-1') {
            return PHP_INT_MAX;
        }

        $unit = strtolower(substr($value, -1));
        $number = (float) $value;

        return (int) match ($unit) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => $number,
        };
    }
}
