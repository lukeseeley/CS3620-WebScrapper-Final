<?php
declare(strict_types=1);

namespace App\Domain\Scheduler;

use App\Domain\Engine\Engine;

class Scheduler implements SchedulerInterface {
    /**
     * @var Engine
     */
    private $engine;

    /**
     * Scheduler Constructor
     * @param string    $initRequest
     */
    public function __construct(string $initRequest = null) {
        $initRequest !== null ? $this->engine = new Engine($initRequest): $this->engine = new Engine();
    }
    
    /**
     * This is the primary function that will run the engine
     * This function will cycle between each phase, until there are no more requests and pages to process
     * At each new cycle, newly aquired requests/pages are then processed
     * Cycles are used for debugging purposes to limit the number of cycles the engine will go through
     * @param int   $cycles
     * @return void
     */
    public function runEngine(int $cycles = null): void {
        $this->startEngine();
        if($cycles !== null && $cycles > 0) {
            $cycle = 0; //The current cycle sequence
            while((count($this->engine->getRequests()) > 0 || count($this->engine->getPages()) > 0) && $cycle < $cycles) { //Are there still more unprocessed requests/pages?
                $this->downloaderPhase();
                $this->spiderPhase();
                $cycle++;
            }
        }
        else {
            while(count($this->engine->getRequests()) > 0 || count($this->engine->getPages()) > 0) { //Are there still more unprocessed requests/pages?
                $this->spiderPhase();
                $this->downloaderPhase();
            }
        }
    }

    /**
     * This will begin processing all aquired requests in the Engine's pending requests
     * This will keep running until there are not any more requsts to process currently
     * @return void
     */
    public function downloaderPhase(): void {
        while(count($this->engine->getRequests()) > 0) {
            $this->engine->sendRequest(); //Send all requests currenly in que
        }
    }

    /**
     * This will begin processing all newly aquired pages and send them to the spider
     * This will keep running until there are no more pages to process
     * This function sends a page to the spider, and then calls to retreive the data for storage
     * That being new url's found on the page, and possibly an item
     * @return void
     */
    public function spiderPhase(): void {
        while(count($this->engine->getPages()) > 0) {
            $this->engine->sendPage(); //Send the spider a page taken from a request
            $this->engine->takeRequests(); //Retreive processed requests from spider
            $this->engine->takeItem(); //Retreive an item from the spider (if any)
        }
    }

    /**
     * This will begin the process of sending all local copied items to the database
     * @return void
     */
    public function itemPipelinePhase(): void {
        $this->engine->pushItems();
        $this->engine->pushRequests();
    }

    /**
     * @return Engine
     */
    public function getEngine(): Engine {
        return $this->engine;
    }

    /**
     * @return void
     */
    public function startEngine(): void {
        $this->engine->sendInitRequest();
    }

    /**
     * @param string    $request
     * @return void
     */
    public function initializeEngine(string $request) {
        $this->engine->setInitRequest($request);
    }
}