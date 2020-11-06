/*
 * Copyright 2019 Luciano Iam <lucianito@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

(function () {
	// JavascriptChannel not available in flutter_webview_plugin
	// simulate it using url fragments    
	if (typeof window.FlutterHost !== 'object') {
		window.FlutterHost = {
			postMessage: function (message) {
				window.location.hash = 'flutterHost_' + message;
			}
		};
	}

	// Add support to include arguments in messages
	FlutterHost.postMessageWithArgs = function (message, args) {
		// stringify guarantees proper Date() serialization
		window._messageArgs = JSON.stringify(args);
		FlutterHost.postMessage(message);
	};
})();
