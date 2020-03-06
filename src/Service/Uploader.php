<?php

namespace App\Service;



use Symfony\Component\HttpFoundation\File\UploadedFile;

class Uploader
{
    private $photoDirectory;

    //$photoDirectory a été déclaré dans service.yaml en bind)
    public function __construct($photoDirectory)
    {
        $this->photoDirectory = $photoDirectory;
    }

    //Enregistrer 1 image
    //si on met :string la methode doit retourner du string
    public function savePhoto(UploadedFile $file) :string
    {
        //Construire un nom de fichier unique
        $fileName = date('YmdHis') . uniqid() . '.' . $file->guessClientExtension();
        $path = $this ->photoDirectory . '/' . $fileName;
        //Enregistrer le fichier
        $file->move($this->photoDirectory, $fileName);
        // Retourner le nom du fichier
        return $fileName;
    }

    //Remplacer une image

    public function replacePhoto(UploadedFile $file, string $oldFile) :string
    {
        //Enregistrer la nouvelle image
        $fileName = $this->savePhoto($file);

        //Supprimer l'ancienne
        $this->deletePhoto($oldFile);

        //Retourner le nom du nouveau fichier
        return $fileName;

    }

    //Supprimer 1 photo
    //si on met :void = la methode ne doit rien retourner
    public function deletePhoto(string $fileName) :void {

        $oldPath = $this->photoDirectory . '/' . $fileName;
        if(file_exists($oldPath) &&is_file($oldPath)){
            unlink($oldPath);
        }
    }
}