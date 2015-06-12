var wg2 = (function(){
	return {
		"pos":0,
		"init":function(){
			this.pNode = document.getElementById('pNode');
			this.defNode = document.getElementById('defNode');
			this.rows = []
		},
		"putRows":function(rows){
			this.rows = this.rows.concat(rows);
		},
		"timeToYmdHis":function(time){
			var date = new Date(time);
			
			var yyyy = date.getFullYear().toString();                                    
			var mm = (date.getMonth()+1).toString(); // getMonth() is zero-based         
			var dd  = date.getDate().toString();             
			var hours = date.getHours().toString();
			var minutes = date.getMinutes().toString();
			var seconds = date.getSeconds().toString();

			return yyyy + '-' + (mm[1]?mm:"0"+mm[0]) + '-' + (dd[1]?dd:"0"+dd[0])
				+" "+ (hours[1]?hours:"0"+hours[0])+":"+ (minutes[1]?minutes:"0"+minutes[0])+":"+ (seconds[1]?seconds:"0"+seconds[0]);
		},
		//-- 노드 하나를 생성(li)
		"createNode":function(row){
			var node = this.defNode.cloneNode(true);
			node.info = row;
			node.dataset["wg2Basename"] = row['basename'];
			node.dataset["wg2Type"] = row['type'];
			node.className = "finfo finfo-"+row["type"];
			if(row['is_image']){
				node.className+=' finfo-image';
			}
			var img = node.getElementsByTagName('img')[0];
			var img_a = img.parentNode;
			var previewbox = node.getElementsByClassName ('previewbox')[0];
			previewbox.dataset.wg2Headcomment='';
			previewbox.dataset.wg2Footcomment='';
			var a = node.getElementsByTagName('a')[0];
			a.dataset["wg2Basename"] = row['basename'];
			a.id="node-"+row['rel_path'];
			a.href="#node-"+row['rel_path'];
			a.title = row['basename'];
			
			var editurl = node.getElementsByClassName('editurl')[0];
			//console.log(row['basename'],row['editurl']);
			if(row['editurl'] == undefined){
				editurl.parentNode.removeChild(editurl);
			}else{
				
				editurl.href =  row['editurl'];
				editurl.target="_blank";
			}
			
			//node.title = row['basename'];
			if(row['type']=='error'){
				a.appendChild(document.createTextNode(row['basename']));
				//node.img = img;
				img_a.parentNode.removeChild(img_a);
				
				previewbox.appendChild(document.createTextNode(row['error_msg']));
			}else if(row['type']=='dir'){
				a.appendChild(document.createTextNode(row['basename']+" ("+row['in_contents_count']+")"));
				//node.img = img;
				img_a.parentNode.removeChild(img_a);
				
				//node.img.src = './img/dir.gif';
				a.href="?dir="+encodeURIComponent(row['rel_path']);
				if(row['in_contents'] && row['in_contents'].length > 0){
					previewbox.appendChild(this.createNodes(row['in_contents'],row['basename']));
				}else{
					previewbox.appendChild(document.createTextNode("[DIR] "+row['basename']));
				}
				previewbox.dataset.wg2Footcomment=this.timeToYmdHis(row["mtime"]*1000);
			}else if(row['type']=='file'){
				a.appendChild(document.createTextNode(row['basename']));
				node.img = img;
				node.img.alt = row['basename'];
				node.img.src = './img/file.gif';
				img.previewbox = previewbox;//
				if(row['is_image']){
					//node.img.src = row['previewurl'];
					node.img.dataset.wg2Previewurl = row['previewurl'];
					node.img.dataset.wg2Ispreview = "0";
				}
				a.href =  row['downurl'];
				a.target="_blank";
				img_a.href =  row['viewurl'];
				img_a.target="_blank";
				previewbox.dataset.wg2Footcomment=this.timeToYmdHis(row["mtime"]*1000);
				
			}
			return node;
		},
		//-- 노드 그룹을 생성(div)
		"createNodes":function(rows,dir){
			var div = document.createElement('div');
			div.dataset.wg2Dir = dir;
			for(var i=0,m=rows.length;i<m;i++){
				div.appendChild(this.createNode(rows[i]));
			}
			return div
			//var div
		},
		//-- 하나씩 출력하기
		"appendNode":function(i){
			if(i==undefined){
				i = this.pos;
				this.pos++;
			}
			if(i>=this.rows.length){
				console.log("더이상 남은 정보가 없습니다.");
				return false;
			}
			var n = this.createNode(this.rows[i])
			this.pNode.appendChild(n);
			return true;
		},
		"boundInfo":function(el){
			var ret = {}; 
			var sl = this.scrollInfo();
			var rect = el.getBoundingClientRect(); 
			///*
			ret.width = rect.right - rect.left; 
			ret.height = rect.bottom - rect.top; 
			ret.left = rect.left + sl.left
			ret.top = rect.top + sl.top;
			ret.bottom = rect.bottom + sl.top;
			//*/
			//ret = rect;
			return ret;
		},
		"scrollInfo":function(){
			return {
			  "left":document.documentElement.scrollLeft||document.body.scrollLeft
			 ,"top":document.documentElement.scrollTop||document.body.scrollTop
			 ,"width":document.documentElement.scrollWidth||document.body.scrollWidth
			 ,"height":document.documentElement.scrollHeight||document.body.scrollHeight
			}
		},
		//-- 스크롤이 오면 이미지 보여주기
		"showPreview":function(){
			var els = document.querySelectorAll("img[data-wg2-ispreview='0']");
			//alert(els.length);
			var postBottom = document.getElementById('postBottom');
			var pbRet = this.boundInfo(postBottom);
			
			var scInfo = this.scrollInfo();
			var scT = scInfo.top;
			var scB = scInfo.top+pbRet.bottom;
			var scTop = scInfo.height-scInfo.top;
			
			for(var i=0,m=els.length;i<m;i++){
				var el = els[i];
				var ret = this.boundInfo(el);
				if( el.dataset.wg2Ispreview  != "0"){
					continue;
				}else if((scInfo.top <= ret.top && ret.top<= pbRet.bottom)
					|| (scInfo.top <= ret.bottom && ret.bottom<= pbRet.bottom)
					){
					//console.log("보임");
					el.onload = function(){
						this.previewbox.dataset.wg2Headcomment=this.naturalWidth+"x"+ this.naturalHeight;
						//setTimeout(function(){this.onload=null},0)
						this.dataset.wg2Ispreview = "2";
					}
					el.src=el.dataset.wg2Previewurl;
					el.dataset.wg2Ispreview = "1";
					
				}else{
					//console.log("x보임");
				}
			}
		},
	}
})()