<?php

//덱에 카드를 52개를 먼저 만들어서 하나씩 꺼낸다.
//1. 게임 시작시 유저와 딜러는 카드를 2개 받는다.
// 1-1. 이때 유저 또는 딜러의 카드 합이 21이면 결과 출력
//2. 카드 합이 21을 초과하면 패배
// 2-1. 유저 또는 딜러의 카드의 합이 21이 넘으면 결과 바로 출력
// 2-2. 둘다 21이 넘을 경우 유저패배
//4. 카드의 계산은 아래의 규칙을 따른다.
// 4-1.카드 2~9는 그 숫자대로 점수
// 4-2. K·Q·J,10은 10점
// 4-3. A는 1점 또는 11점 둘 중의 하나로 (승리에 유리한 방향으로) 계산
//5. 카드의 합이 같으면 다음의 규칙에 따름
// 5-1. 카드수가 적은 쪽이 승리
// 5-2. 카드수가 같을경우 스페이드>크로버>다이아>하트 순
//6. 유저가 카드를 받을 때 딜러는 아래의 규칙을 따른다.
// 6-1. 딜러는 카드의 합이 17보다 낮을 경우 카드를 한장 더 받는다
// 6-2. 17 이상일 경우는 받지 않는다.
//7. 1입력 : 카드 더받기, 2입력 : 카드비교후 결과출력, 0입력 : 게임종료
//fscanf(STDIN, "%d\n", $input); 로 입력값을 터미널로 받아서 게임 플레이
//2를 입력해서 결과가 출력되어도 0을 입력하거나 카드를 다 쓰지 않으면 게임 종료되지 않음.
//앞의 게임에서 한번이라도 사용한 카드는 게임이 종료되고 재시작될때까지 중복사용 불가능


// 초기 카드 덱 생성
function createDeck($newDeck = false)
{
    static $deck; // 정적(static) 변수로 선언
    if ($newDeck || empty($deck)) { // 덱이 비어있다면 또는 새로운 덱이 필요한 경우
        $deck = array();
        $suits = array('스페이드', '하트', '다이아', '클로버');
        $faces = array('2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A');
        foreach ($suits as $suit) {
            foreach ($faces as $face) {
                $deck[] = array('face' => $face, 'suit' => $suit);
            }
        }
        shuffle($deck); // 덱을 무작위로 섞음
    }
    return $deck;
}

// 카드 한 장을 뽑음
function drawCard(&$deck)
{
    if (empty($deck)) { // 덱이 비어있다면 새로운 덱을 만듦
        $deck = createDeck(true);
    }
    return array_shift($deck);
}

// 카드의 합을 계산
function calculateHandValue($hand)
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
    // 에이스 처리
    for ($i = 0; $i < $aceCount; $i++) {
        if ($value + 11 <= 21) {
            $value += 11;
        } else {
            $value += 1;
        }
    }
    return $value;
}



// 유저에게 카드를 뽑음
function userDrawCard(&$deck, &$userHand) {
    $userHand[] = drawCard($deck);
    echo "당신의 카드: ";
    foreach ($userHand as $card) {
        echo $card['face'] . $card['suit'] . " ";
    }
    echo "\n";
}

// 딜러에게 카드를 뽑음
function dealerDrawCard(&$deck, &$dealerHand) {
    $dealerHand[] = drawCard($deck);
    foreach ($dealerHand as $card) {
        return $card['face'] . $card['suit'] . " ";
    }
}

//카드 확인
function getCardString($card)
{
    return $card['face'] . $card['suit'] . " ";
}

function getHandString($hand)
{
    $handString = "";
    foreach ($hand as $card) {
        $handString .= getCardString($card);
    }
    return $handString;
}


// 게임 실행

function playBlackjack()
{
    $deck = createDeck(true);
    $playerHand = array();
    $dealerHand = array();
    $playerHand[] = drawCard($deck);
    $playerHand[] = drawCard($deck);
    $dealerHand[] = drawCard($deck);
    $dealerHand[] = drawCard($deck);
    $playerTotal = calculateHandValue($playerHand);
    $dealerTotal = calculateHandValue($dealerHand, true);
    echo "딜러의 카드: " . getCardString($dealerHand[0]) . "\n";
    echo "플레이어의 카드: " . getHandString($playerHand) . "\n";
    while (true) {
        $input = fscanf(STDIN, "%d\n", $input); 
        if ($input == 1) {
            $playerHand[] = drawCard($deck);
            $playerTotal = calculateHandValue($playerHand);
            echo "당신의 카드: " . getHandString($playerHand) . "\n";
            if ($playerTotal > 21) {
                echo "21점을 초과했습니다. 딜러의 승리입니다.\n";
                return;
            }
        } else if ($input == 2) {
            while ($dealerTotal < 17) {
                $dealerHand[] = drawCard($deck);
                $dealerTotal = calculateHandValue($dealerHand, true);
            }
            echo "딜러의 카드: " . calculateHandValue($dealerHand) . "\n";
            if ($dealerTotal > 21) {
                echo "딜러가 21점을 초과하여 패배했습니다. 축하합니다!\n";
                return;
            }
            if ($playerTotal == $dealerTotal) {
                echo "무승부입니다.\n";
                return;
            } else if ($playerTotal > $dealerTotal) {
                echo "축하합니다. 이겼습니다!\n";
                return;
            } else {
                echo "딜러의 승리입니다. 다시 도전하세요.\n";
                return;
            }
        } else if ($input == 0) {
            echo "게임을 종료합니다.\n";
            return;
        } else {
            echo "잘못된 입력입니다. 다시 입력해주세요.\n";
        }
    }
    // 게임 종료 후 덱을 초기화하고 다시 시작하면 52장의 덱으로 재설정
    $deck = createDeck(true);
}




?>