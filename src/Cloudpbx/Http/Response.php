<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Http;

interface Response
{
    /**
     * @return int
     */
    public function statusCode();

    /**
     * @return mixed
     */
    public function body();
}
