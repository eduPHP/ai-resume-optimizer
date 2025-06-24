<?php

namespace App\DTO;

use ArrayIterator;
use Illuminate\Contracts\Support\Arrayable;
use IteratorAggregate;
use Traversable;

class AIInputOptions implements Arrayable, IteratorAggregate
{
    public function __construct(
        public bool $makeGrammaticalCorrections = false,
        public bool $changeProfessionalSummary = false,
        public bool $generateCoverLetter = false,
        public bool $changeTargetRole = false,
        public bool $mentionRelocationAvailability = false,
        public string $roleName = '',
        public string $roleDescription = '',
        public ?string $roleLocation = '',
        public string $roleCompany = ''
    ) {
    }

    public function toArray(): array
    {
        $config = [
            $this->changeProfessionalSummary ? [
                'role' => 'system',
                'content' => 'should replace the Professional Summary section with a role specific summary emphasizing the candidate\'s strengths (keep the title)'
            ] : false,
            $this->changeTargetRole ? [
                'role' => 'system',
                'content' => "should replace the \"Target Role\" (below the name on title) with: {$this->roleName}"
            ] : false,
            $this->mentionRelocationAvailability ? [
                'role' => 'system',
                'content' => "at the bottom, using the class \"footer\" on the element, should add \"Available for remote work or relocation to [country] through visa sponsorship\" where the country is: {$this->roleLocation}"
            ] : false,
            $this->makeGrammaticalCorrections ? [
                'role' => 'system',
                'content' => 'should make grammatical corrections'
            ] : false,
        ];

        return array_filter($config);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->toArray());
    }
}
