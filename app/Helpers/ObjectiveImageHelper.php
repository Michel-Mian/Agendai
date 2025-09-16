<?php

namespace App\Helpers;

class ObjectiveImageHelper
{
    /**
     * Mapeia nomes de objetivos para suas respectivas imagens
     * 
     * @param string $objetivoNome
     * @return string
     */
    public static function getImagePath($objetivoNome)
    {
        $imageMap = [
            // Objetivos principais oferecidos na criação de viagem
            'Cultura e história' => 'imgs/objectives/open-book.png',
            'Gastronomia' => 'imgs/objectives/restaurant.png',
            'Aventura' => 'imgs/objectives/hiking.png',
            'Natureza' => 'imgs/objectives/landscape.png',
            'Praia' => 'imgs/objectives/beach-umbrella.png',
            'Vida noturna' => 'imgs/objectives/moon.png',
            'Compras' => 'imgs/objectives/shopping-cart.png',
            'Arte e museus' => 'imgs/objectives/museum.png',
            
            // Fallbacks para objetivos adicionais (usando imagens existentes)
            'Negócios' => 'imgs/objectives/open-book.png',
            'Relaxamento' => 'imgs/objectives/beach-umbrella.png',
            'Turismo religioso' => 'imgs/objectives/open-book.png',
            'Esportes' => 'imgs/objectives/hiking.png',
            'Fotografia' => 'imgs/objectives/landscape.png',
            'Família' => 'imgs/objectives/beach-umbrella.png',
        ];

        return $imageMap[$objetivoNome] ?? 'imgs/objectives/open-book.png';
    }

    /**
     * Retorna todas as imagens disponíveis
     * 
     * @return array
     */
    public static function getAvailableImages()
    {
        return [
            'adventure.png',
            'beach-umbrella.png',
            'business.png',
            'default.png',
            'hiking.png',
            'landscape.png',
            'moon.png',
            'museum.png',
            'nightlife.png',
            'open-book.png',
            'relaxation.png',
            'restaurant.png',
            'shopping-cart.png',
            'shopping.png'
        ];
    }

    /**
     * Verifica se uma imagem existe fisicamente
     * 
     * @param string $imagePath
     * @return bool
     */
    public static function imageExists($imagePath)
    {
        $fullPath = public_path($imagePath);
        return file_exists($fullPath);
    }
}
