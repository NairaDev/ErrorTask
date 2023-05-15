<?php

//$key должен быть  приватным
//  В методе translate_text используется $this->key, но метод объявлен как статический,
//   Это приведет к ошибке, поскольку статические методы не имеют доступа к 
//   нестатическим свойствам. Можно сделать метод нестатическим или передавать ключ в качестве
// аргумента.
//  culr_settop вместо curl_setopt и Curl_close вместо curl_close.чтобы вызовы функций были 
//  корректными.
//  Код не проверяет наличие обязательных параметров text и lang. 
//  Метод init и метод translate_text не проверяют
//  наличие ключа API перед его использованием. 



class Translation {
    const DETECT_YA_URL = 'https://translate.yandex.net/api/v1.5/tr.json/detect';
    const TRANSLATE_YA_URL = 'https://translate.yandex.net/api/v1.5/tr.json/translate';

    private $key;

    public function __construct($key) {
        $this->key = $key;
    }

    public function translateText($text, $lang, $format = "text") {
        if (empty($this->key)) {
            throw new InvalidArgumentException("Field 'key' is required");
        }

        $values = array(
            'key' => $this->key,
            'text' => $text,
            'lang' => $lang,
            'format' => ($format == "text") ? "plain" : $format,
        );

        $formData = http_build_query($values);

        $ch = curl_init(self::TRANSLATE_YA_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $formData);

        $json = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($json, true);
        if ($data['code'] == 200) {
            return $data['text'];
        } else {
            throw new RuntimeException("Translation failed: " . $data['message']);
        }
    }
}

try {
    $key = "AIzalyCf2zgkmk-nRxbB4gg49M9GZhmFei55uo";
    $translation = new Translation($key);

    $text = "fly";
    $lang = "ru";

    $translatedText = $translation->translateText($text, $lang);
    echo "Переведенный текст: " . $translatedText;
} catch (Throwable $e) {
    echo "Ошибка перевода: " . $e->getMessage();
}








?>