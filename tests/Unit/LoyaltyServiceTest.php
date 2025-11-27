<?php

namespace Tests\Unit;

use App\Services\LoyaltyService;
use Tests\TestCase;

class LoyaltyServiceTest extends TestCase
{
    /** @test */
    public function point_redemption_value_calculation_is_correct()
    {
        $loyaltyService = new LoyaltyService();
        
        // 100 puntos * $100 por punto = $10,000
        $this->assertEquals(10000, $loyaltyService->calculateRedemptionValue(100));
        
        // 50 puntos * $100 = $5,000
        $this->assertEquals(5000, $loyaltyService->calculateRedemptionValue(50));
    }

    /** @test */
    public function points_needed_calculation_is_correct()
    {
        $loyaltyService = new LoyaltyService();
        
        // $10,000 / $100 por punto = 100 puntos
        $this->assertEquals(100, $loyaltyService->calculatePointsNeeded(10000));
        
        // $15,000 / $100 por punto = 150 puntos
        $this->assertEquals(150, $loyaltyService->calculatePointsNeeded(15000));
    }

    /** @test */
    public function level_info_returns_correct_data()
    {
        $loyaltyService = new LoyaltyService();
        
        $bronzeInfo = $loyaltyService->getLevelInfo('bronze');
        $this->assertEquals(0, $bronzeInfo['min_points']);
        $this->assertEquals(1.0, $bronzeInfo['multiplier']);
        
        $goldInfo = $loyaltyService->getLevelInfo('gold');
        $this->assertEquals(2000, $goldInfo['min_points']);
        $this->assertEquals(1.5, $goldInfo['multiplier']);
        $this->assertContains('birthday_bonus', $goldInfo['benefits']);
    }
}
