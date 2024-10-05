# Yireo MagewireTestUtils

**A library of testing utilities (among which PHPUnit assertions) for building integration tests for Magewire-driven Magento extensions**

Current status: Draft

### Installation
```bash
composer require yireo/magento2-magewire-test-utils
```

### Usage
Extend your own integration test case from `MagewireComponentTestCase`:
```php
namespace Yireo\Example\Test\Integration\Magewire;

use Magento\Framework\App\Response\Http;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsEnabled;
use Yireo\MagewireTestUtils\Test\Integration\MagewireComponentTestCase;
use Yireo\Example\Magewire\MyExample;

class MyExampleTest extends MagewireComponentTestCase
{
    use AssertModuleIsEnabled;
    
    /**
     * @return void
     * @magentoAppArea frontend
     */
    public function testComponentResponse()
    {
        $this->assertModuleIsEnabled('Yireo_Example');
        $this->assertModuleIsEnabled('Magewirephp_Magewire');
        
        $this->assertMagewireComponentResolves(
            'content_schedule_block0',
            MyExample::class,
            ['cms_index_index']
        );
        
        $this->dispatch('/');
        
        /** @var Http $response */
        $response = $this->getResponse();
        $body = $response->getBody();
        $this->assertMagewireBlockExistsInHtml('content_schedule_block0', $body);
    }
}
```
