const fs=require('fs'), path=require('path'), cp=require('child_process');
function walk(dir) {return fs.readdirSync(dir,{withFileTypes:true}).flatMap(e=>e.isDirectory()?walk(path.join(dir,e.name)):[path.join(dir,e.name)]);}
const missing=new Map(), errors=[];
for(const file of walk('app').filter(f=>f.endsWith('.php'))) {
 const s=fs.readFileSync(file,'utf8');
 for(const m of s.matchAll(/use (App\\[^; ]+);/g)) {
  const ref=m[1].replace(/^App/,'app').replaceAll('\\','/')+'.php';
  if(!fs.existsSync(ref)) missing.set(m[1], [...(missing.get(m[1])||[]),file]);
 }
 const lint=cp.spawnSync('php',['-l',file],{encoding:'utf8'});
 if(lint.status!==0) errors.push({file,error:lint.stdout+lint.stderr});
}
const assets=[];
for(const file of ['resources/views/layouts/css_styles.blade.php','resources/views/layouts/js_library.blade.php']) {
 for(const m of fs.readFileSync(file,'utf8').matchAll(/asset\(\s*['"]([^'"]+)['"]\s*\)/g)) if(!fs.existsSync(path.join('public',m[1])))assets.push(m[1]);
}
const jsErrors=[];
for(const file of fs.readdirSync('public/js').filter(f=>f.endsWith('.js'))) {
 const result=cp.spawnSync(process.execPath,['--check',path.join('public/js',file)],{encoding:'utf8'});
 if(result.status!==0) jsErrors.push({file,error:result.stderr});
}
const report={missingImports:Object.fromEntries(missing),phpErrors:errors,jsErrors,missingLayoutAssets:assets};
fs.writeFileSync('scripts/onix-check.json',JSON.stringify(report,null,2));
console.log(JSON.stringify(report,null,2));
