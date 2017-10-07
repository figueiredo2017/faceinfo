<?php
/**
 * @author Figueiredo Luiz <lffigueiredo@gmail.com>
 */

namespace AppBundle\Controller;
use AppBundle\Entity\Faces;
use AppBundle\Form\FacesType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Unirest;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $face = new Faces();
        $form = $this->createForm(FacesType::class, $face);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $face->getFace();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move(
                $this->getParameter('faces_directory'),
                $fileName
            );
            $face->setFace($fileName);
            return $this->redirectToRoute('result', ['face' => $fileName]);
        }
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/result", name="result")
     */
    public function resultAction(Request $request)
    {
        $apiKey = $this->getParameter('fpp_api_key');
        $apiSecret = $this->getParameter('fpp_api_secret');

        $output = array();

        $file = $this->getParameter('faces_directory').'/'.$request->query->get('face');
        $url = 'https://api-us.faceplusplus.com/facepp/v3/detect';
        $headers = array('Accept' => 'application/json');
        $parameters = [
            'api_key' => $apiKey,
            'api_secret' => $apiSecret,
            'image_base64' => base64_encode(file_get_contents($file))
        ];
        $response = Unirest\Request::post($url, $headers, $parameters);

        $faces = $response->body->faces;

        $output['total_detected'] = count($faces);

        $count = 0;
        $faceTokens = '';
        foreach ($faces as $face) {
            if ($count<5) {
                $count++;
                if ($count > 1) $faceTokens .= ',';
                $faceTokens .= $face->face_token;
            }
        }


        $url = 'https://api-us.faceplusplus.com/facepp/v3/face/analyze';
        $headers = array('Accept' => 'application/json');
        $parameters = [
            'api_key' => $apiKey,
            'api_secret' => $apiSecret,
            'face_tokens' => $faceTokens,
            'return_attributes' => 'gender,age,ethnicity'
        ];
        $detail = Unirest\Request::post($url, $headers, $parameters);

        $detail = $detail->body->faces;
        foreach($detail as $face) {
            $output['details'][] = [
              'gender' => $face->attributes->gender->value,
                'age' => $face->attributes->age->value,
              'ethnicity' => $face->attributes->ethnicity->value,
            ];
        };
        return $this->render('default/result.html.twig', [
            'face' => $request->query->get('face'),
            'result' => $output
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function aboutAction(Request $request)
    {
        return $this->render('default/about.html.twig', []);
    }
}
