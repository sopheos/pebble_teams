<?php

namespace Pebble\Teams;

/**
 * AdaptiveCard
 *
 * https://adaptivecards.io/explorer/
 */
class AdaptiveCard implements CardInterface
{
    private string $summary = 'Card';
    private array $content = [];

    // -------------------------------------------------------------------------
    // Main setters
    // -------------------------------------------------------------------------

    public function summary(string $value): static
    {
        $this->summary = $value;
        return $this;
    }

    public function set(string $key, mixed $value): static
    {
        if (self::isAcceptedKey($key)) {
            $this->content[$key] = $value;
        }
        return $this;
    }

    public function append(string $key, mixed $value): static
    {
        if (self::isAcceptedKey($key)) {
            $this->content[$key][] = $value;
        }

        return $this;
    }

    // -------------------------------------------------------------------------
    // Shortcuts
    // -------------------------------------------------------------------------

    /**
     * Ajoute un bloc de text
     *
     * config.color : default, dark, light, accent, good, warning, attention
     * config.size : default, small, medium, large, extraLarge
     * config.weight : default,lighter,bolder
     *
     * https://adaptivecards.io/explorer/TextBlock.html
     *
     * @param string $text
     * @param array $config
     * @return static
     */
    public function text(string $text, array $config = []): static
    {
        return $this->append('body', [
            'type' => 'TextBlock',
            'text' => $text,
        ] + $config);
    }

    /**
     * Ajoute une série de données de type clé/valeur
     *
     * https://adaptivecards.io/explorer/FactSet.html
     *
     * @param string $text
     * @param array $config
     * @return static
     */
    public function facts(array $facts): static
    {
        $data = [];
        foreach ($facts as $k => $v) {
            $data[] = [
                'title' => $k,
                'value' => $v,
            ];
        }

        return $this->append('body', [
            'type' => 'FactSet',
            'facts' => $data,
        ]);
    }

    /**
     * Ajoute une image
     *
     * https://adaptivecards.io/explorer/Image.html
     *
     * @param string $uri
     * @param array $config
     * @return static
     */
    public function image(string $uri, array $config = []): static
    {
        return $this->append('body', [
            'type' => 'Image',
            'text' => $uri,
        ] + $config);
    }

    /**
     * Ajout un bouton d'action pour ouvrir un lien
     *
     * config.style : default, positive, destructive
     *
     * https://adaptivecards.io/explorer/Action.OpenUrl.html
     *
     * @param string $title
     * @param string $url
     * @param array $config
     * @return static
     */
    public function openUrl(string $title, string $url, array $config = []): static
    {
        return $this->append('action', [
            "type" => "Action.OpenUrl",
            "title" => $title,
            "url" => $url
        ] + $config);
    }

    // -------------------------------------------------------------------------
    // Getters
    // -------------------------------------------------------------------------

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function getContent(): array
    {
        return $this->content;
    }


    public function getMessage(): array
    {
        $card = [
            "contentType" => "application/vnd.microsoft.card.adaptive",
            "contentUrl" => null,
            "content" => [
                "\$schema" => "http://adaptivecards.io/schemas/adaptive-card.json",
                "type" => "AdaptiveCard",
                "version" => "1.3",
            ] + $this->content,
        ];

        return [
            "type" => "message",
            "attachments" => [$card],
            "summary" => $this->summary,
        ];
    }

    // -------------------------------------------------------------------------
    // Tools
    // -------------------------------------------------------------------------

    private static function isAcceptedKey(string $key): bool
    {
        return in_array($key, [
            "body",
            "actions",
            "selectAction",
            "fallbackText",
            "backgroundImage",
            "minHeight",
            "speak",
            "lang",
            "verticalContentAligment",
        ]);
    }

    // -------------------------------------------------------------------------
}
