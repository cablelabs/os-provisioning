<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
?>
@if (Module::collections()->has(['Ticketsystem']))
<script src="{{ mix('js/ticketsystem.js') }}"></script>
@endif
@if (Module::collections()->has(['ProvBase']))
<script src="{{ mix('js/prov-base.js') }}"></script>
@endif
@if (Module::collections()->has(['ProvMon']))
<script src="{{ mix('js/prov-mon.js') }}"></script>
@endif
@if (Module::collections()->has(['HfcBase']))
<script src="{{ mix('js/hfc-base.js') }}"></script>
@endif
