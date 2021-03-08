<?php

namespace App\Controller;

use App\Entity\Media;
use App\Repository\MediaRepository;
use App\Service\Encryptor;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class MediaController extends AbstractController
{
    /**
     * @Route("/api/admin/media", name="get_medias", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getMedias(
        MediaRepository $repository,
        Request $request,
        SerializerInterface $serializer,
        string $publicImagesPath
    ): JsonResponse {
        $page = (int) $request->get('page', 1);
        $itemsPerPage = (int) $request->get('size', 20);

        $medias = $repository->findAllByPage($page, $itemsPerPage);

        $total = count($medias);
        foreach ($medias as $key => $media) {
            if ($media->getFilename()) {
                $media->setFilename('/'.$publicImagesPath.'/'.$media->getFilename());
            }
            $medias[$key] = $serializer->normalize($media, Media::class);
        }

        return new JsonResponse([
            'total' => $total,
            'items' => $medias
        ]);
    }

    /**
     * @Route("/api/admin/media/{media}", name="get_media", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getMediaById(
        Media $media,
        SerializerInterface $serializer,
        string $publicImagesPath
    ): JsonResponse {
        $media->setFilename('/'.$publicImagesPath.'/'.$media->getFilename());

        $dto = $serializer->serialize($media, 'json');

        return JsonResponse::fromJsonString($dto);
    }

    /**
     * @Route("/api/admin/media", name="create_media", methods={"POST"})
     *
     * @return JsonResponse
     */
    public function createMedia(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        FileUploader $fileUploader,
        string $publicImagesPath
    ): JsonResponse {
        $data = json_decode($request->get('dto'), true);
        /**
         * @var UploadedFile $file
         */
        $file = $request->files->get('file');
        $data['filename'] = '';
        if ($file) {
            $data['type'] = $file->getMimeType();
            $filename = $fileUploader->upload($file);
            $data['filename'] = $filename;
        }

        var_dump($data);
        /**
         * @var Media $dto
         */
        $dto = $serializer->denormalize(
            $data,
            Media::class,
            ''
        );

        $em->persist($dto);
        $em->flush();

        $dto->setFilename('/'.$publicImagesPath.'/'.$dto->getFilename());

        return JsonResponse::fromJsonString(
            $serializer->serialize($dto, 'json')
        );
    }

    /**
     * @Route("/api/admin/media/{media}", name="edit_media", methods={"PUT"})
     *
     * @return JsonResponse
     */
    public function editMediaById(
        Media $media,
        EntityManagerInterface $em,
        Request $request,
        SerializerInterface $serializer,
        FileUploader $fileUploader
    ): JsonResponse {
        $mediaJson = $request->get('dto');
        $data = json_decode($mediaJson, true);
        /**
         * @var UploadedFile $file
         */
        $file = $request->files->get('file');
        $data['filename'] = $media->getFilename();
        if ($file) {
            $data['type'] = $file->getMimeType();
            $filename = $fileUploader->upload($file);
            $data['filename'] = $filename;
        }

        /**
         * @var Media $dto
         */
        $dto = $serializer->denormalize(
            $data,
            Media::class,
            null,
            [ AbstractNormalizer::OBJECT_TO_POPULATE => $media ]
        );

        $em->persist($dto);
        $em->flush();

        return JsonResponse::fromJsonString(
            $serializer->serialize($dto, 'json')
        );
    }

    /**
     * @Route("/api/admin/media/{media}", name="delete_media", methods={"DELETE"})
     *
     * @return JsonResponse
     */
    public function deleteMedia(
        Media $media,
        EntityManagerInterface $em,
        Filesystem $filesystem,
        string $publicImagesPath
    ): JsonResponse {
        $em->remove($media);
        $em->flush();

        if ($media->getFilename()) {
            $filesystem->remove('/' . $publicImagesPath . '/' . $media->getFilename());
        }

        return new JsonResponse([]);
    }
}
