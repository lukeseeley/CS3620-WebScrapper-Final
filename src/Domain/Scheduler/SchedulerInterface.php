<?php
declare(strict_types=1);

namespace App\Domain\Scheduler;

use App\Domain\Engine\Engine;

interface SchedulerInterface {
    /**
     * @param int   $cycles
     * @return void
     * This is the primary function that will run the engine
     * Cycles are used for debugging purposes to limit the number of cycles the engine will go through
     */
    public function runEngine(int $cycles = null): void;

    /**
     * @return void
     * This will begin processing all aquired requests in the Engine's pending requests
     */
    public function downloaderPhase(): void;

    /**
     * @return void
     * This will begin processing all newly aquired pages and send them to the spider
     */
    public function spiderPhase(): void;

    /**
     * @return void
     * This will begin the process of sending all local copied items to the database
     */
    public function itemPipelinePhase(): void;

    /**
     * @return Engine
     */
    public function getEngine(): Engine;

    /**
     * @return void
     */
    public function startEngine(): void;

    /**
     * @param string    $request
     * @return void
     */
    public function initializeEngine(string $request);
}