<?php

namespace Angelov\Eestec\Platform\Members\SocialProfiles;

use Angelov\Eestec\Platform\Members\Member;
use Illuminate\Database\Eloquent\Model;

class SocialProfile extends Model
{
    protected $table = "social_profiles";

    public function member()
    {
        return $this->belongsTo('Angelov\Eestec\Platform\Members\Member');
    }

    /**
     * @return Member
     */
    public function getMember()
    {
        return $this->member;
    }

    public function setMember(Member $member)
    {
        $this->member()->associate($member);
    }

    public function getProvider()
    {
        return $this->getAttribute('provider');
    }

    public function setProvider($provider)
    {
        $this->setAttribute('provider', $provider);
    }

    public function getProfileId()
    {
        return $this->getAttribute('profile_id');
    }

    public function setProfileId($profileId)
    {
        $this->setAttribute('profile_id', $profileId);
    }
}
