<?php

namespace App\Controller;

use App\Utils\Link;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

class DefaultController extends AbstractController {
    #[Route('/', name: 'app_index')]
    #[Template('default/index.html.twig')]
    public function index(): void {
    }

    /**
     * @return array<mixed>
     */
    #[Route('/links', name: 'app_links')]
    #[Template('utility/links.html.twig')]
    public function links(): array {
        $config = __DIR__.'/../../config/links.yaml';
        if (!file_exists($config)) {
            throw $this->createNotFoundException('link.yaml resource could not be found');
        }
        $links = Yaml::parseFile($config);
        $return = ['links' => []];
        foreach ($links as $heading => $linkSet) {
            $return['links'][$heading] = [];
            foreach ($linkSet as $link) {
                $return['links'][$heading][] = new Link($link['link'], $link['description']);
            }
        }

        return $return;
    }
}
