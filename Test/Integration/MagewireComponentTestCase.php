<?php

declare(strict_types=1);

namespace Yireo\MagewireTestUtils\Test\Integration;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\TestCase\AbstractController;
use Magewirephp\Magewire\Model\ComponentResolver;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsEnabled;

class MagewireComponentTestCase extends AbstractController
{
    use AssertModuleIsEnabled;
    
    public function assertMagewireComponentResolves(
        string $blockName,
        string $componentClass,
        array $additionalHandles = [],
    ) {
        $this->assertModuleIsEnabled('Magewirephp_Magewire');
        
        $componentResolver = ObjectManager::getInstance()->get(ComponentResolver::class);
        $layout = ObjectManager::getInstance()->get(LayoutInterface::class);
        
        foreach ($additionalHandles as $additionalHandle) {
            $layout->getUpdate()->addHandle($additionalHandle);
        }
        
        $layout->generateXml();
        $layout->generateElements();
        $block = $layout->getBlock($blockName);
        
        $this->assertInstanceOf(AbstractBlock::class, $block);
        
        $component = $componentResolver->resolve($block);
        $this->assertInstanceOf($componentClass, $component);
    }
    
    public function assertMagewireExistsInHtml(string $html)
    {
        // @todo: This is not working, unless you use Hyva or use Magewire_RequireJs
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