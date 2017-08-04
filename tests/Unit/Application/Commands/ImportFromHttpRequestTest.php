<?php

namespace TheRestartProject\RepairDirectory\Tests\Unit\Application\Commands;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use TheRestartProject\RepairDirectory\Application\Commands\Business\ImportFromHttpRequest\ImportFromHttpRequestCommand;
use TheRestartProject\RepairDirectory\Application\Commands\Business\ImportFromHttpRequest\ImportFromHttpRequestHandler;
use TheRestartProject\RepairDirectory\Domain\Models\Business;
use TheRestartProject\RepairDirectory\Domain\Repositories\BusinessRepository;
use TheRestartProject\RepairDirectory\Domain\Services\BusinessGeocoder;
use TheRestartProject\RepairDirectory\Tests\TestCase;
use \Mockery as m;

/**
 * Test for the CreateFromHttpRequestTest command
 *
 * @category Test
 * @package  TheRestartProject\RepairDirectory\Tests\Unit\Application\Commands
 * @author   Joaquim d'Souza <joaquim@outlandish.com>
 * @license  GPLv2 https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 * @link     http://tactician.thephpleague.com/
 */
class ImportFromHttpRequestTest extends TestCase
{
    /**
     * The Handler under test
     *
     * @var ImportFromHttpRequestHandler
     */
    private $handler;

    /**
     * The mocked repository
     *
     * @var m\MockInterface
     */
    private $repository;

    /**
     * The mocked Geocoder
     *
     * @var m\MockInterface
     */
    private $geocoder;

    /**
     * Setups the the handler under test for each test
     *
     * @return void
     */
    public function setUp()
    {
        $this->repository = m::spy(BusinessRepository::class);
        $this->geocoder = m::mock(BusinessGeocoder::class);

        /**
         * Cast mock to BusinessRepository
         *
         * @var BusinessRepository $repository
         */
        $repository = $this->repository;

        /**
         * Cast mock to Geocoder
         *
         * @var BusinessGeocoder $geocoder
         */
        $geocoder = $this->geocoder;

        $this->handler = new ImportFromHttpRequestHandler(
            $repository,
            $geocoder
        );
    }

    /**
     * Tests that it can be constructed
     *
     * This test confirms that the handler can be constructed with
     * the correct dependencies.
     *
     * @test
     *
     * @return void
     */
    public function it_can_be_constructed()
    {
        /**
         * Cast mock to BusinessRepository
         *
         * @var BusinessRepository $repository
         */
        $repository = m::spy(BusinessRepository::class);

        /**
         * Cast mock to Geocoder
         *
         * @var BusinessGeocoder $geocoder
         */
        $geocoder = m::mock(BusinessGeocoder::class);

        $handler = new ImportFromHttpRequestHandler(
            $repository,
            $geocoder
        );
        self::assertInstanceOf(ImportFromHttpRequestHandler::class, $handler);
    }

    /**
     * Tests that the handler successfully converts a data array
     * into a Business using the factory and adds it to the
     * Repository.
     *
     * @test
     *
     * @return void
     */
    public function it_can_add_a_business_to_the_repository()
    {
        $data = [];
        $this->setupGeocoder();

        $addedBusiness = $this->handler->handle(new ImportFromHttpRequestCommand($data));

        $this->assertBusinessAdded($addedBusiness);
        self::assertInstanceOf(Business::class, $addedBusiness);
    }


    /**
     * Tests that the handler successfully updates an existing business from
     * a data array.
     *
     * @test
     *
     * @return void
     */
    public function it_can_update_an_existing_business()
    {
        $data = [
            'name' => 'New Name'
        ];
        $business = new Business();
        $business->setName('Old Name');

        $this->setupGeocoder();
        $this->repository->shouldReceive('get')->with(1)->andReturn($business);

        $updatedBusiness = $this->handler->handle(new ImportFromHttpRequestCommand($data, 1));

        $this->repository->shouldHaveReceived('get')->with(1);
        $this->repository->shouldNotHaveReceived('add');
        self::assertInstanceOf(Business::class, $updatedBusiness);
        self::assertEquals('New Name', $business->getName());
    }

    /**
     * Sets up the geocoder to return a [lat, lng] for a Business
     *
     * @return void
     */
    public function setupGeocoder()
    {
        $this->geocoder->shouldReceive('geocode')->andReturn([0, 0]);
    }

    /**
     * Asserts that the business was added to the repository
     *
     * @param Business $business the Business model that was added to the repository
     *
     * @return void
     */
    public function assertBusinessAdded($business)
    {
        $this->repository->shouldHaveReceived('add')
            ->with($business);
    }
}