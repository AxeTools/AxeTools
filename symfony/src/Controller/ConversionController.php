<?php

namespace App\Controller;

use App\Entity\TextEntry;
use App\Entity\UuidEntry;
use App\Form\ConvertEntryType;
use App\Form\SerializeEntryType;
use App\Form\UuidEntryType;
use App\Utils\Flash;
use App\Utils\Type\SerializeType;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

#[Route('/convert', name: 'app_convert_')]
final class ConversionController extends AbstractController {
    public const string UUID_DECODER_DATETIME_FORMAT = 'Y-m-d H:i:s.u.0 e';

    /**
     * @return array<mixed>
     */
    #[Route('/fromJson', name: 'fromJson')]
    #[Template('conversion/fromString.html.twig')]
    public function fromJson(Request $request): array {
        $textEntry = new TextEntry();
        $form = $this->createForm(ConvertEntryType::class, $textEntry);
        $result = null;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TextEntry $textEntry */
            $textEntry = $form->getData();

            try {
                $result = json_decode($textEntry->getData(), true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->addFlash(Flash::ALERT_WARNING, 'Unable to parse JSON data: '.$e->getMessage());
            }
        }

        return [
            'title' => 'JSON Decode',
            'form' => $form,
            'result' => $result,
        ];
    }

    /**
     * @return array<mixed>
     */
    #[Route('/fromSerialize', name: 'fromSerialize')]
    #[Template('conversion/fromString.html.twig')]
    public function fromSerialize(Request $request): array {
        $textEntry = new TextEntry();
        $form = $this->createForm(ConvertEntryType::class, $textEntry);
        $result = null;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TextEntry $textEntry */
            $textEntry = $form->getData();

            try {
                $result = unserialize($textEntry->getData());
            } catch (\Throwable $e) {
                $this->addFlash(Flash::ALERT_WARNING, 'Unserialize Error: '.$e->getMessage());
            }
        }

        return [
            'title' => 'PHP Serialized String',
            'form' => $form,
            'result' => $result,
        ];
    }

    /**
     * @return array<mixed>
     */
    #[Route('/fromYaml', name: 'fromYaml')]
    #[Template('conversion/fromString.html.twig')]
    public function fromYaml(Request $request): array {
        $textEntry = new TextEntry();
        $form = $this->createForm(ConvertEntryType::class, $textEntry);
        $result = null;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TextEntry $textEntry */
            $textEntry = $form->getData();

            try {
                $result = Yaml::parse($textEntry->getData());
            } catch (\Throwable $e) {
                $this->addFlash(Flash::ALERT_WARNING, 'Unserialize Error: '.$e->getMessage());
            }
        }

        return [
            'title' => 'YAML String',
            'form' => $form,
            'result' => $result,
        ];
    }

    /**
     * @return array<mixed>
     */
    #[Route('/fromUrl', name: 'fromUrl')]
    #[Template('conversion/fromString.html.twig')]
    public function convertFromUrl(Request $request): array {
        $textEntry = new TextEntry();
        $form = $this->createForm(ConvertEntryType::class, $textEntry);
        $result = null;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TextEntry $textEntry */
            $textEntry = $form->getData();

            try {
                $result = urldecode($textEntry->getData());
                // if there is key value pares in the data we can parse them, if not it is just a URL-encoded string
                if (str_contains($result, '=')) {
                    $result_out = [];
                    parse_str($result, $result_out);
                    $result = $result_out;
                }
            } catch (\Throwable $e) {
                $this->addFlash(Flash::ALERT_WARNING, 'Unserialize Error: '.$e->getMessage());
            }
        }

        return [
            'title' => 'URL String',
            'form' => $form,
            'result' => $result,
        ];
    }

    /**
     * @return array<mixed>
     */
    #[Route('/toString', name: 'toString')]
    #[Template('conversion/toString.html.twig')]
    public function toString(Request $request): array {
        $textEntry = new TextEntry();
        $form = $this->createForm(SerializeEntryType::class, $textEntry);
        $result = null;
        $output = '';

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TextEntry $textEntry */
            $textEntry = $form->getData();
            /*
             * eval can do a LOT! so the output buffer is utilized here to ensure that no text is transmitted which would
             * screw up the Response processor since the headers would be sent before the echo text.  This will trap any
             * output and allow it to be processed.
             */
            ob_start();
            try {
                eval($textEntry->getData());
            } catch (\Throwable $e) {
                // This is an attempt to catch any and all php errors to display them and not cause the controller to fail
                $this->addFlash(Flash::ALERT_ERROR, 'Your <code>php</code> code caused the following exception to be thrown: <code>'.$e->getMessage().'</code>');
            } finally {
                // collect any output that the buffer trapped before the script completed or an error was generated
                $output = ob_get_contents();
                ob_end_clean();
            }

            if ('' !== $output && is_string($output)) {
                $this->addFlash(Flash::ALERT_WARNING, 'Your script had output text:<br> <pre>'.htmlspecialchars($output, ENT_QUOTES | ENT_SUBSTITUTE).'</pre>');
            }
            /**
             * This error is the result of eval() intending to update the $result variable, there is no way for phpstan
             * to know if this happens or not, which is the point of the test.
             *
             * @author rhowe
             *
             * @since 2023-10-03
             *
             * @phpstan-ignore-next-line
             *
             * @psalm-suppress RedundantCondition
             */
            if (!is_array($result) && SerializeType::TYPE_URL !== $textEntry->getDumpType()) {
                $this->addFlash(Flash::ALERT_ERROR, 'The <code>$result</code> variable must have an <code>array</code> type assigned to it');
            }
        } else {
            $this->addFlash(Flash::ALERT_DANGER, "This utilizes the <code>eval()</code> function, it can be <span class='font-weight-bold text-danger'>DANGEROUS!</span> please be thoughtful.");
            $this->addFlash(Flash::ALERT_INFO, 'The <code>php</code> executed <span class="font-weight-bold">MUST</span> assign an array value to the <code>$result</code> variable.');
        }

        return [
            'title' => 'Serialize to String',
            'form' => $form,
            'result' => $result,
        ];
    }

    /**
     * @return array<mixed>
     */
    #[Route('/fromUuid', name: 'fromUuid')]
    #[Template('conversion/fromUuid.html.twig')]
    public function fromUuid(Request $request): array {
        $uuidEntry = new UuidEntry();
        $form = $this->createForm(UuidEntryType::class, $uuidEntry);
        $result = null;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UuidEntry $uuidEntry */
            $uuidEntry = $form->getData();

            ob_start();

            $uuid = $uuidEntry->getUuid();
            passthru('uuid -d '.escapeshellarg($uuid));
            $results = ob_get_clean();
            if (is_string($results)) {
                $results = trim($results);
            } else {
                $results = '';
            }

            $result = $this->parseUuidDecodeResults($results);

            $result = $this->formatUuidResult($result);
        }

        return [
            'title' => 'Uuid Decoder',
            'form' => $form,
            'result' => $result,
        ];
    }

    /**
     * @return array<mixed>
     */
    protected function parseUuidDecodeResults(string $input): array {
        $rows = explode("\n", $input);

        $output = [
            'encode' => [
                'STR' => substr($rows[0], 17),
                'SIV' => substr($rows[1], 17),
            ],
            'decode' => [
                'version' => substr($rows[3], 17),
                'variant' => substr($rows[2], 17),
            ],
        ];

        if (str_contains($rows[4], 'time:')) {
            $output['decode']['contents'] = [
                'time' => \DateTimeImmutable::createFromFormat(self::UUID_DECODER_DATETIME_FORMAT, substr($rows[4], 24)),
                'clock' => substr($rows[5], 24),
                'node' => substr($rows[6], 24),
            ];
        } else {
            $output['decode']['contents'] = implode("\n", [substr($rows[4], 17), substr($rows[5], 17)]);
        }

        return $output;
    }

    /**
     * @param array<string, mixed> $result
     *
     * @return array<mixed>
     */
    protected function formatUuidResult(array $result): array {
        $hasTime = dotHas($result, 'decode.contents.time');

        return [
            'version' => (int) $result['decode']['version'],
            'hasTime' => $hasTime,
            'uuid' => $result['encode']['STR'],
            'decoded' => $result,
        ];
    }
}
