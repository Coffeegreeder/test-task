<?php

namespace app\models;

use Yii;
use yii\helpers\Inflector;
use yii\helpers\VarDumper;
use yii\web\UploadedFile;

/**
 * This is the model class for table "files".
 *
 * @property int $id
 * @property string $uploaded_at
 * @property string $filename
 * @property string $path
 */
class Files extends \yii\db\ActiveRecord
{
    public $uploads = [];
    public $file;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uploaded_at', 'file'], 'safe'],
            [['uploaded_at'], 'default', 'value' => date('Y-m-d H:i:s')],
            [['filename'], 'required'],
            [['filename'], 'string', 'max' => 255],
            // [['path'], 'string', 'max' => 512],
            [['uploads'], 'file', 'skipOnEmpty' => true, 'extensions' => ['png', 'jpeg', 'jpg', 'svg', 'bmp', 'webp', 'jfif',], 'maxFiles' => 5],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID записи',
            'uploaded_at' => 'Дата загрузки',
            'filename' => 'Название файла',
            'uploads' => 'Загружаемые файлы',
        ];
    }

    // Сохранение файлов и записей
    public function pushFiles()
    {
        if (!empty($this->uploads)) {
            $connection = Yii::$app->getDb();
            $transaction = $connection->beginTransaction();
            $files = UploadedFile::getInstances($this, 'uploads');
            $path = 'uploads/';

            $this->createDirectory($path);

            try {
                foreach ($files as $file) {
                    $fileName = $this->genFileName($file, $path);
                    $file->saveAs($path . $fileName);

                    $connection->createCommand()
                        ->insert(
                            'files',
                            [
                                'filename' => $fileName,
                                'uploaded_at' => date('Y-m-d H:i:s'),
                            ],
                        )
                        ->execute();
                }

                $transaction->commit();

                return true;
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            } catch (\Throwable $e) {
                $transaction->rollBack();
                throw $e;
            }
        }
    }

    // Создание директории (если не существует)
    function createDirectory($path)
    {
        if (!file_exists($path)) mkdir($path, 0775, true);
    }

    // Генерация уникального названия файла
    public function genFileName($file, $path)
    {

        $name = Inflector::transliterate($file->baseName);

        if (!preg_match('#[а-яё]#i', $name)) {
            $name = $this->customTranslit($file->baseName);
        }

        if (file_exists($path . $name . $file->extension)) $name = $name . '_' . substr(md5(microtime() . rand(0, 1000)), 0, 8);

        return strtolower($name) . '.' . $file->extension;
    }

    // Если по какой-то причине не работает модуль PHP intl 
    function customTranslit($value)
    {
        $converter = array(
            'а' => 'a',    'б' => 'b',    'в' => 'v',    'г' => 'g',    'д' => 'd',
            'е' => 'e',    'ё' => 'e',    'ж' => 'zh',   'з' => 'z',    'и' => 'i',
            'й' => 'y',    'к' => 'k',    'л' => 'l',    'м' => 'm',    'н' => 'n',
            'о' => 'o',    'п' => 'p',    'р' => 'r',    'с' => 's',    'т' => 't',
            'у' => 'u',    'ф' => 'f',    'х' => 'h',    'ц' => 'c',    'ч' => 'ch',
            'ш' => 'sh',   'щ' => 'sch',  'ь' => '',     'ы' => 'y',    'ъ' => '',
            'э' => 'e',    'ю' => 'yu',   'я' => 'ya',

            'А' => 'A',    'Б' => 'B',    'В' => 'V',    'Г' => 'G',    'Д' => 'D',
            'Е' => 'E',    'Ё' => 'E',    'Ж' => 'Zh',   'З' => 'Z',    'И' => 'I',
            'Й' => 'Y',    'К' => 'K',    'Л' => 'L',    'М' => 'M',    'Н' => 'N',
            'О' => 'O',    'П' => 'P',    'Р' => 'R',    'С' => 'S',    'Т' => 'T',
            'У' => 'U',    'Ф' => 'F',    'Х' => 'H',    'Ц' => 'C',    'Ч' => 'Ch',
            'Ш' => 'Sh',   'Щ' => 'Sch',  'Ь' => '',     'Ы' => 'Y',    'Ъ' => '',
            'Э' => 'E',    'Ю' => 'Yu',   'Я' => 'Ya',
        );

        $value = strtr($value, $converter);
        return $value;
    }
}
