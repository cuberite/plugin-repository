var makeBSS = function (el) {
	var $slideshows = document.querySelectorAll(el), // a collection of all of the slideshow
		$slideshow = {},
		Slideshow = {
			init: function (el) {
				this.counter = 0; // to keep track of current slide
				this.el = el; // current slideshow container
				this.$items = el.querySelectorAll('figure'); // a collection of all of the slides, caching for performance
				this.numItems = this.$items.length; // total number of slides
				this.injectControls(el);
				this.addEventListeners(el);
			},
			showCurrent: function (i) {
				// increment or decrement this.counter depending on whether i === 1 or i === -1
				if (i > 0) {
					this.counter = (this.counter + 1 === this.numItems) ? 0 : this.counter + 1;
				} else {
					this.counter = (this.counter - 1 < 0) ? this.numItems - 1 : this.counter - 1;
				}

				[].forEach.call(this.$items, function (el) {					
					// and remove any outdated resize and opaqueness listeners
					this.removeImageEventListener(el);
				}, this);
				
				// add a resize and opaqueness listener
				this.addImageEventListener(el);
			},
			injectControls: function (el) {
			// build and inject prev/next controls
				// first create all the new elements
				var spanPrev = document.createElement("span"),
					spanNext = document.createElement("span"),
					docFrag = document.createDocumentFragment();

				// add classes
				spanPrev.classList.add('bss-prev');
				spanNext.classList.add('bss-next');

				// add contents
				spanPrev.innerHTML = '&laquo;';
				spanNext.innerHTML = '&raquo;';

				// append elements to fragment, then append fragment to DOM
				docFrag.appendChild(spanPrev);
				docFrag.appendChild(spanNext);
				el.appendChild(docFrag);
			},
			addEventListeners: function (el) {
				var that = this;
				el.querySelector('.bss-next').addEventListener('click', function () {
					that.showCurrent(1); // increment & show
				}, false);

				el.querySelector('.bss-prev').addEventListener('click', function () {
					that.showCurrent(-1); // decrement & show
				}, false);
				
				this.addImageEventListener(el);

				el.onkeydown = function (e) {
					e = e || window.event;
					if (e.keyCode === 37) {
						that.showCurrent(-1); // decrement & show
					} else if (e.keyCode === 39) {
						that.showCurrent(1); // increment & show
					}
				};
			},
			addImageEventListener: function (el) {
				var currentimage = this.$items[this.counter].children[0];				
				if (currentimage.complete) // only if image has finished loading (our onload resizing is complete):
				{
					// dynamically adjust background box height (limited by css)
					this.el.style.height = currentimage.height + "px";
					currentimage.style.opacity = 1; // make opaque the required figure
				}
				else
				{
					// else, add a listener to change when its ready
					var that = this;
					currentimage.addEventListener('load', function() {
						el.style.height = currentimage.height + "px"; // dynamically adjust background box height (limited by css) when loaded
						currentimage.style.opacity = 1; // make opaque the required figure
					});
				}
			},
			removeImageEventListener: function(el) {
				var currentimage = el.children[0];	
				currentimage.removeEventListener('load', this.addImageEventListener);
				currentimage.style.opacity = 0; // make transparent the required figure
			}
		};

	// make instances of Slideshow as needed
	[].forEach.call($slideshows, function (el) {
		$slideshow = Object.create(Slideshow);
		$slideshow.init(el);
	});
};