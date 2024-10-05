<?php

declare(strict_types=1);

namespace Yireo\MagewireTestUtils\Test\Integration;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\TestCase;

class MagewireComponentTestCase extends TestCase
{
    public function assertMagewireComponentResolves(string $blockName, string $componentClass)
    {
        $componentResolver = ObjectManager::getInstance()->get(\Magewirephp\Magewire\Model\ComponentResolver::class);
        $layout = ObjectManager::getInstance()->get(LayoutInterface::class);
        $block = $layout->getBlock($blockName);
        
        $component = $componentResolver->resolve($block);
        $this->assertInstanceOf($component instanceof $componentClass);
    }
    
    public function assertMagewireExistsInHtml(string $html)
    {
        $this->assertStringContainsString('Magewirephp_Magewire/js/livewire.js', $html);
        $this->assertStringContainsString('window.magewire', $html);
    }
    
    public function assertMagewireBlockExistsInHtml(
        string $blockName,
        string $html) {
        $this->assertStringContainsString('wire:id="'.$blockName.'"', $html);
        
        preg_match_all('/wire:initial-data=\"([^"]+)\"/', $html, $matches);
        $this->assertNotEmpty($matches);
        
        $componentData = false;
        foreach ($matches[1] as $matchIndex => $match) {
            $matchData = json_decode(html_entity_decode($match), true);
            if ($matchData['fingerprint']['id'] === $blockName) {
                $componentData = $matchData;
                break;
            }
        }
        
        $this->assertNotEmpty($componentData);
        $this->assertEquals($blockName, $componentData['fingerprint']['name']);
        // @todo: What else to do with this component data?
    }
}