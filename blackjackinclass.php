<?php
class Blackjack
{
    private static $suits = array('스페이드', '하트', '다이아', '클로버');
    private static $faces = array('2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A');

    private $deck;
    private $playerHand;
    private $dealerHand;

    public function __construct()
    {
        $this->createDeck(true);
        $this->playerHand = array();
        $this->dealerHand = array();
        $this->playerHand[] = $this->drawCard();
        $this->playerHand[] = $this->drawCard();
        $this->dealerHand[] = $this->drawCard();
        $this->dealerHand[] = $this->drawCard();
    }

    private function createDeck($newDeck = false)
    {
        if ($newDeck || empty($this->deck)) {
            $this->deck = array();
            foreach (self::$suits as $suit) {
                foreach (self::$faces as $face) {
                    $this->deck[] = array('face' => $face, 'suit' => $suit);
                }
            }
            shuffle($this->deck);
        }
    }

    private function drawCard()
    {
        if (empty($this->deck)) {
            $this->createDeck(true);
        }
        return array_shift($this->deck);
    }

    private function calculateHandValue($hand, $isDealer = false)
    {
        $value = 0;
        $aceCount = 0;
        foreach ($hand as $card) {
            if ($card['face'] == 'A') {
                $aceCount++;
            } elseif (in_array($card['face'], array('K', 'Q', 'J', '10'))) {
                $value += 10;
            } else {
                $value += intval($card['face']);
            }
        }
        for ($i = 0; $i < $aceCount; $i++) {
            if ($value + 11 <= 21) {
                $value += 11;
            } else {
                $value += 1;
            }
        }
        if ($isDealer && count($hand) == 2 && $value >= 17 && $value <= 21) {
            $value += 10;
        }
        return $value;
    }

    private function getCardString($card)
    {
        return $card['face'] . $card['suit'] . " ";
    }

    private function getHandString($hand)
    {
        $handString = "";
        foreach ($hand as $card) {
            $handString .= $this->getCardString($card);
        }
        return $handString;
    }

    public function play()
    {   
        echo "딜러의 카드 중 하나만 오픈: " . $this->getCardString($this->dealerHand[0]) . "\n";
        echo "당신의 카드: " . $this->getHandString($this->playerHand) . "\n";
        while (true) {
            echo "다시뽑기(1) or 결과보기(2)? ";
            $input = intval(trim(fgets(STDIN)));
            if ($input == 1) {
                $this->playerHand[] = $this->drawCard();
                echo "당신의 카드: " . $this->getHandString($this->playerHand) . "\n";
                if ($this->calculateHandValue($this->playerHand) > 21) {
                    echo "파산! 당신의 패배!.\n";
                    return;
                }
            } else if ($input == 2) {
                break;
            }
        }
        $dealerValue = $this->calculateHandValue($this->dealerHand);
        while ($dealerValue < 17) {
            $this->dealerHand[] = $this->drawCard();
            $dealerValue = $this->calculateHandValue($this->dealerHand);
        }
        echo "딜러의 카드: " . $this->getHandString($this->dealerHand) . "\n";
        if ($dealerValue > 21) {
            echo "딜러 버스트! 당신 승리!\n";
        } else {
            $playerValue = $this->calculateHandValue($this->playerHand);
            if ($playerValue > $dealerValue) {
                echo "당신 승리!\n";
            } else if ($playerValue < $dealerValue) {
                echo "당신 패배!\n";
            } else {
                echo "무승부!\n";
            }
        }
    }
    public function isGameOver()
    {
        $playerValue = $this->calculateHandValue($this->playerHand);
        $dealerValue = $this->calculateHandValue($this->dealerHand);
        return $playerValue > 21 || $dealerValue > 21;
    }

}
    // create a new instance of the Blackjack class
    $game = new Blackjack();

    // start the game
    $game->play();


?>