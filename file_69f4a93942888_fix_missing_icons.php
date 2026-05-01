<?php
/**
 * Script de détection et récupération automatique des icônes manquantes
 * pour projets React Native/Expo
 * Auteur: AfricaSoftt DEV
 */

class IconFixer {
    private $projectPath;
    private $requiredIcons = [
        'icon.png' => ['size' => 512, 'background' => '#0A2B3E', 'text' => 'ASD'],
        'splash-icon.png' => ['size' => 1242, 'background' => '#0A2B3E', 'text' => 'AfricaSoftt'],
        'adaptive-icon.png' => ['size' => 512, 'background' => '#0A2B3E', 'text' => 'ASD'],
        'favicon.png' => ['size' => 64, 'background' => '#0A2B3E', 'text' => 'A']
    ];
    
    private $imageAPIs = [
        'https://ui-avatars.com/api/?background=%s&color=fff&size=%s&fontsize=%s&name=%s&length=%s&rounded=true',
        'https://placehold.co/%s/%s/white?text=%s'
    ];
    
    public function __construct($projectPath = '.') {
        $this->projectPath = rtrim($projectPath, '/');
        $this->checkDependencies();
    }
    
    private function checkDependencies() {
        if (!extension_loaded('gd')) {
            die("❌ L'extension GD est requise. Installez-la avec: sudo apt-get install php-gd\n");
        }
        if (!extension_loaded('curl')) {
            die("❌ L'extension cURL est requise. Installez-la avec: sudo apt-get install php-curl\n");
        }
    }
    
    public function scanAndFix() {
        echo "\n🔍 AfricaSoftt DEV - Fixeur d'icônes\n";
        echo "=====================================\n\n";
        
        // Créer le dossier assets s'il n'existe pas
        $assetsPath = $this->projectPath . '/assets';
        if (!file_exists($assetsPath)) {
            mkdir($assetsPath, 0777, true);
            echo "✅ Dossier 'assets' créé\n";
        }
        
        $fixed = 0;
        $errors = 0;
        
        foreach ($this->requiredIcons as $iconName => $config) {
            $iconPath = $assetsPath . '/' . $iconName;
            
            if (file_exists($iconPath)) {
                echo "✓ $iconName existe déjà\n";
                continue;
            }
            
            echo "⚠️  $iconName manquant - Téléchargement en cours...\n";
            
            if ($this->fetchIcon($iconName, $config, $iconPath)) {
                echo "✅ $iconName téléchargé avec succès\n";
                $fixed++;
            } else {
                // Fallback: création locale
                if ($this->generateLocalIcon($iconPath, $config)) {
                    echo "✅ $iconName généré localement\n";
                    $fixed++;
                } else {
                    echo "❌ Échec pour $iconName\n";
                    $errors++;
                }
            }
            echo "\n";
        }
        
        $this->showSummary($fixed, $errors);
        $this->updateAppConfig();
    }
    
    private function fetchIcon($iconName, $config, $iconPath) {
        // Méthode 1: UI Avatars API
        $encodedText = urlencode($config['text']);
        $apiUrl = sprintf(
            $this->imageAPIs[0],
            ltrim($config['background'], '#'),
            $config['size'],
            round($config['size'] / 3),
            $encodedText,
            strlen($config['text']),
            $config['size']
        );
        
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $imageData = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $imageData && file_put_contents($iconPath, $imageData)) {
            return true;
        }
        
        // Méthode 2: Placehold.co
        $apiUrl2 = sprintf(
            $this->imageAPIs[1],
            $config['size'],
            ltrim($config['background'], '#'),
            $encodedText
        );
        
        $ch2 = curl_init($apiUrl2);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch2, CURLOPT_TIMEOUT, 30);
        
        $imageData2 = curl_exec($ch2);
        $httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
        curl_close($ch2);
        
        if ($httpCode2 === 200 && $imageData2 && file_put_contents($iconPath, $imageData2)) {
            return true;
        }
        
        return false;
    }
    
    private function generateLocalIcon($iconPath, $config) {
        if (!function_exists('imagecreatetruecolor')) {
            return false;
        }
        
        $size = $config['size'];
        $img = imagecreatetruecolor($size, $size);
        
        // Convertir la couleur hex en RGB
        list($r, $g, $b) = sscanf($config['background'], "#%02x%02x%02x");
        $bgColor = imagecolorallocate($img, $r, $g, $b);
        $textColor = imagecolorallocate($img, 255, 255, 255);
        
        // Remplir le fond
        imagefilledrectangle($img, 0, 0, $size, $size, $bgColor);
        
        // Calculer la taille de la police
        $fontSize = $size / 2.5;
        $text = substr($config['text'], 0, 3);
        
        // Dessiner le texte
        $bbox = imagettfbbox($fontSize, 0, $this->getFontPath(), $text);
        $textWidth = $bbox[2] - $bbox[0];
        $textHeight = $bbox[1] - $bbox[7];
        $x = ($size - $textWidth) / 2;
        $y = ($size - $textHeight) / 2 + $textHeight;
        
        imagettftext($img, $fontSize, 0, $x, $y, $textColor, $this->getFontPath(), $text);
        
        // Sauvegarder
        imagepng($img, $iconPath);
        imagedestroy($img);
        
        return file_exists($iconPath);
    }
    
    private function getFontPath() {
        // Chemins possibles pour les polices système
        $fonts = [
            '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
            '/System/Library/Fonts/Helvetica.ttc',
            'C:\Windows\Fonts\Arial.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf'
        ];
        
        foreach ($fonts as $font) {
            if (file_exists($font)) {
                return $font;
            }
        }
        
        // Retourne null pour utiliser la police intégrée GD
        return null;
    }
    
    private function updateAppConfig() {
        $configPath = $this->projectPath . '/app.json';
        if (!file_exists($configPath)) {
            echo "⚠️  app.json non trouvé\n";
            return;
        }
        
        $config = json_decode(file_get_contents($configPath), true);
        if (!$config) return;
        
        $modified = false;
        
        // Vérifier et mettre à jour les chemins des icônes
        if (!isset($config['expo']['icon']) || $config['expo']['icon'] === '') {
            $config['expo']['icon'] = './assets/icon.png';
            $modified = true;
        }
        
        if (!isset($config['expo']['splash']['image'])) {
            $config['expo']['splash']['image'] = './assets/splash-icon.png';
            $config['expo']['splash']['resizeMode'] = 'contain';
            $config['expo']['splash']['backgroundColor'] = '#0A2B3E';
            $modified = true;
        }
        
        if (!isset($config['expo']['android']['adaptiveIcon']['foregroundImage'])) {
            $config['expo']['android']['adaptiveIcon']['foregroundImage'] = './assets/adaptive-icon.png';
            $config['expo']['android']['adaptiveIcon']['backgroundColor'] = '#0A2B3E';
            $modified = true;
        }
        
        if ($modified) {
            file_put_contents($configPath, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            echo "✅ app.json mis à jour avec les chemins d'icônes\n";
        }
    }
    
    private function showSummary($fixed, $errors) {
        echo "\n📊 RÉSUMÉ\n";
        echo "==========\n";
        echo "✓ Icônes corrigées: $fixed\n";
        if ($errors > 0) {
            echo "❌ Échecs: $errors\n";
        }
        echo "\n🎉 Les icônes sont maintenant prêtes pour le build!\n";
        echo "🔧 Exécutez maintenant: eas build --platform android --profile preview\n\n";
    }
}

// Exécution du script
if (php_sapi_name() === 'cli') {
    $path = $argv[1] ?? __DIR__;
    $fixer = new IconFixer($path);
    $fixer->scanAndFix();
} else {
    echo "Ce script doit être exécuté en ligne de commande.\n";
    echo "Utilisation: php fix_missing_icons.php [chemin_du_projet]\n";
}
?>