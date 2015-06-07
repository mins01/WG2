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
		//-- 노드 하나를 생성(li)
		"createNode":function(row){
			var node = this.defNode.cloneNode(true);
			node.info = row;
			node.dataset["wg2Basename"] = row['basename'];
			node.dataset["wg2Type"] = row['type'];
			node.className = "finfo finfo-"+row["type"];
			var img = node.getElementsByTagName('img')[0];
			var previewbox = node.getElementsByClassName ('previewbox')[0];
			var a = node.getElementsByTagName('a')[0];
			a.dataset["wg2Basename"] = row['basename'];
			
			a.id="node-"+row['rel_path'];
			a.href="#node-"+row['rel_path'];
			a.title = row['basename'];
			a.appendChild(document.createTextNode(row['basename']));
			//node.title = row['basename'];
			if(row['is_dir']){
				img.parentNode.removeChild(img);
				a.href="?dir="+row['rel_path'];
				if(row['in_contents'] && row['in_contents'].length > 0){
					previewbox.appendChild(this.createNodes(row['in_contents'],row['basename']));
				}else{
					previewbox.appendChild(document.createTextNode("[DIR] "+row['basename']));
				}
			}else{
				node.img = img;
				node.img.alt = row['basename'];
				if(row['is_image']){
					node.img.src = row['preview'];
				}
				a.href =  row['preview'];
				a.target="_blank";
			}
			return node;
		},
		//-- 노드 그룹을 생성(ul)
		"createNodes":function(rows,dir){
			var ul = document.createElement('ul');
			ul.dataset.wg2Dir = dir;
			for(var i=0,m=rows.length;i<m;i++){
				ul.appendChild(this.createNode(rows[i]));
			}
			return ul
			//var ul
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
		}
	}
})()